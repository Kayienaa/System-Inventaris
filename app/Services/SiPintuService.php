<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class SiPintuService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected int $timeout;
    protected int $connectTimeout;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('sipintu.api_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');
        $this->clientId = config('sipintu.client_id', 'app_1p03mtss7tbl');
        $this->clientSecret = config('sipintu.client_secret', 'sec_BVKkUc6wG7NbBwP6SD3kGN8DZHiSodCo');
        $this->timeout = (int) config('sipintu.timeout', 60);
        $this->connectTimeout = (int) config('sipintu.connect_timeout', 10);
        $this->cacheTtl = (int) config('sipintu.cache_ttl', 1800);
    }

    /**
     * Cache store dedicated for SiPintu (uses 'file' to avoid MySQL max_allowed_packet limit).
     */
    protected function cacheStore(): \Illuminate\Contracts\Cache\Repository
    {
        try {
            return Cache::store('file');
        } catch (\Throwable $e) {
            return Cache::store();
        }
    }

    /**
     * Base HTTP client with authentication headers.
     */
    protected function client(int $customTimeout = null): \Illuminate\Http\Client\PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($customTimeout ?? $this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->withHeaders([
                'X-Client-ID'     => $this->clientId,
                'X-Client-Secret' => $this->clientSecret,
                'Accept'          => 'application/json',
            ]);
    }

    /**
     * Ping / Heartbeat — cek apakah SiPintu Gateway online dan client terdaftar.
     */
    public function ping(bool $forceRefresh = false): array
    {
        $cacheKey = 'sipintu_ping_' . md5($this->baseUrl . $this->clientId);

        if (!$forceRefresh && $this->cacheStore()->has($cacheKey)) {
            return $this->cacheStore()->get($cacheKey);
        }

        try {
            $response = $this->client(8)->get('/api/v1/ping', [
                'client_id' => $this->clientId,
            ]);

            if ($response->successful()) {
                $result = [
                    'connected' => true,
                    'data'      => $response->json(),
                ];
                $this->safeCachePut($cacheKey, $result, 60);
                return $result;
            }

            $result = [
                'connected' => false,
                'error'     => 'HTTP ' . $response->status(),
                'message'   => $response->json('message', 'Server SiPintu merespon status ' . $response->status()),
            ];
            $this->safeCachePut($cacheKey, $result, 30);
            return $result;
        } catch (ConnectionException $e) {
            return [
                'connected' => false,
                'error'     => 'Connection failed',
                'message'   => 'Tidak dapat terhubung ke SiPintu Gateway: ' . $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'error'     => 'Exception',
                'message'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Ambil seluruh data siswa mentah (dengan file cache 30 menit dan data compression).
     */
    public function getAllStudentsRaw(bool $forceRefresh = false): array
    {
        @ini_set('max_execution_time', 300);
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $cacheKey = 'sipintu_all_students_raw';

        if (!$forceRefresh && $this->cacheStore()->has($cacheKey)) {
            return $this->cacheStore()->get($cacheKey);
        }

        try {
            $response = $this->client(60)->get('/api/v1/sijuna/students');

            if ($response->successful()) {
                $json = $response->json();
                $rawList = $json['data'] ?? (is_array($json) ? $json : []);

                // Compact data using foreach to save memory and avoid duplicate array allocations
                $compactList = [];
                foreach ($rawList as $s) {
                    $compactList[] = [
                        'id'     => $s['id'] ?? null,
                        'nis'    => $s['nis'] ?? null,
                        'nisn'   => $s['nisn'] ?? null,
                        'nama'   => $s['nama'] ?? '-',
                        'jk'     => $s['jk'] ?? null,
                        'hp'     => $s['hp'] ?? null,
                        'alamat' => $s['alamat'] ?? '-',
                        'user'   => [
                            'email' => $s['user']['email'] ?? null,
                            'name'  => $s['user']['name'] ?? null,
                        ],
                    ];
                }
                unset($rawList);

                $result = [
                    'success' => true,
                    'count'   => $json['count'] ?? count($compactList),
                    'data'    => $compactList,
                    'source'  => $json['source'] ?? 'SiPintu Gateway',
                ];

                $this->safeCachePut($cacheKey, $result, $this->cacheTtl);
                return $result;
            }

            return [
                'success' => false,
                'count'   => 0,
                'error'   => 'HTTP ' . $response->status(),
                'message' => $response->json('message', 'Gagal mengambil data siswa.'),
                'data'    => [],
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'count'   => 0,
                'error'   => 'Exception',
                'message' => 'Gagal terhubung ke API Siswa: ' . $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    /**
     * Ambil data siswa dengan filter & pencarian cepat.
     *
     * @param  array  $params  ['nis' => ..., 'search' => ...]
     */
    public function getStudents(array $params = [], bool $forceRefresh = false): array
    {
        $raw = $this->getAllStudentsRaw($forceRefresh);

        if (!$raw['success']) {
            return $raw;
        }

        $items = $raw['data'];
        $nis = trim($params['nis'] ?? '');
        $search = mb_strtolower(trim($params['search'] ?? ''));

        if ($nis !== '') {
            $items = array_values(array_filter($items, function ($item) use ($nis) {
                $itemNis = (string) ($item['nis'] ?? '');
                $itemNisn = (string) ($item['nisn'] ?? '');
                return str_contains($itemNis, $nis) || str_contains($itemNisn, $nis);
            }));
        }

        if ($search !== '') {
            $items = array_values(array_filter($items, function ($item) use ($search) {
                $nama = mb_strtolower((string) ($item['nama'] ?? ''));
                $nis = (string) ($item['nis'] ?? '');
                $email = mb_strtolower((string) ($item['user']['email'] ?? ''));
                $alamat = mb_strtolower((string) ($item['alamat'] ?? ''));
                $hp = (string) ($item['hp'] ?? '');

                return str_contains($nama, $search)
                    || str_contains($nis, $search)
                    || str_contains($email, $search)
                    || str_contains($alamat, $search)
                    || str_contains($hp, $search);
            }));
        }

        return [
            'success'      => true,
            'total'        => $raw['count'],
            'count'        => count($items),
            'data'         => $items,
            'source'       => $raw['source'] ?? 'SiPintu Gateway',
            'is_filtered'  => ($nis !== '' || $search !== ''),
        ];
    }

    /**
     * Ambil seluruh data guru mentah (dengan file cache 30 menit).
     */
    public function getAllTeachersRaw(bool $forceRefresh = false): array
    {
        @ini_set('max_execution_time', 300);
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $cacheKey = 'sipintu_all_teachers_raw';

        if (!$forceRefresh && $this->cacheStore()->has($cacheKey)) {
            return $this->cacheStore()->get($cacheKey);
        }

        try {
            $response = $this->client(60)->get('/api/v1/sijuna/teachers');

            if ($response->successful()) {
                $json = $response->json();
                $rawList = $json['data'] ?? (is_array($json) ? $json : []);

                $compactList = [];
                foreach ($rawList as $t) {
                    $compactList[] = [
                        'id'             => $t['id'] ?? null,
                        'nip'            => $t['nip'] ?? null,
                        'kode'           => $t['kode'] ?? '-',
                        'nama'           => $t['nama'] ?? '-',
                        'nama_panggilan' => $t['nama_panggilan'] ?? null,
                        'jk'             => $t['jk'] ?? null,
                        'hp'             => $t['hp'] ?? null,
                        'alamat'         => $t['alamat'] ?? '-',
                        'status'         => $t['status'] ?? 1,
                        'user'           => [
                            'email' => $t['user']['email'] ?? null,
                            'name'  => $t['user']['name'] ?? null,
                        ],
                    ];
                }
                unset($rawList);

                $result = [
                    'success' => true,
                    'count'   => $json['count'] ?? count($compactList),
                    'data'    => $compactList,
                    'source'  => $json['source'] ?? 'SiPintu Gateway',
                ];

                $this->safeCachePut($cacheKey, $result, $this->cacheTtl);
                return $result;
            }

            return [
                'success' => false,
                'count'   => 0,
                'error'   => 'HTTP ' . $response->status(),
                'message' => $response->json('message', 'Gagal mengambil data guru.'),
                'data'    => [],
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'count'   => 0,
                'error'   => 'Exception',
                'message' => 'Gagal terhubung ke API Guru: ' . $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    /**
     * Ambil data guru dengan filter & pencarian cepat.
     *
     * @param  array  $params  ['nip' => ..., 'search' => ...]
     */
    public function getTeachers(array $params = [], bool $forceRefresh = false): array
    {
        $raw = $this->getAllTeachersRaw($forceRefresh);

        if (!$raw['success']) {
            return $raw;
        }

        $items = $raw['data'];
        $nip = trim($params['nip'] ?? '');
        $search = mb_strtolower(trim($params['search'] ?? ''));

        if ($nip !== '') {
            $items = array_values(array_filter($items, function ($item) use ($nip) {
                $itemNip = (string) ($item['nip'] ?? '');
                $itemKode = (string) ($item['kode'] ?? '');
                return str_contains($itemNip, $nip) || str_contains($itemKode, $nip);
            }));
        }

        if ($search !== '') {
            $items = array_values(array_filter($items, function ($item) use ($search) {
                $nama = mb_strtolower((string) ($item['nama'] ?? ''));
                $panggilan = mb_strtolower((string) ($item['nama_panggilan'] ?? ''));
                $nip = (string) ($item['nip'] ?? '');
                $kode = mb_strtolower((string) ($item['kode'] ?? ''));
                $email = mb_strtolower((string) ($item['user']['email'] ?? ''));
                $alamat = mb_strtolower((string) ($item['alamat'] ?? ''));
                $hp = (string) ($item['hp'] ?? '');

                return str_contains($nama, $search)
                    || str_contains($panggilan, $search)
                    || str_contains($nip, $search)
                    || str_contains($kode, $search)
                    || str_contains($email, $search)
                    || str_contains($alamat, $search)
                    || str_contains($hp, $search);
            }));
        }

        return [
            'success'     => true,
            'total'       => $raw['count'],
            'count'       => count($items),
            'data'        => $items,
            'source'      => $raw['source'] ?? 'SiPintu Gateway',
            'is_filtered' => ($nip !== '' || $search !== ''),
        ];
    }

    /**
     * Ambil statistik ringkasan SiPintu untuk admin dashboard.
     */
    public function getDashboardSummary(): array
    {
        $ping = $this->ping();
        $isConnected = $ping['connected'] ?? false;

        $studentCount = 2306;
        $teacherCount = 71;

        if ($isConnected) {
            if ($this->cacheStore()->has('sipintu_all_students_raw')) {
                $studentCount = $this->cacheStore()->get('sipintu_all_students_raw')['count'] ?? $studentCount;
            }
            if ($this->cacheStore()->has('sipintu_all_teachers_raw')) {
                $teacherCount = $this->cacheStore()->get('sipintu_all_teachers_raw')['count'] ?? $teacherCount;
            } else {
                $teachers = $this->getAllTeachersRaw();
                if ($teachers['success']) {
                    $teacherCount = $teachers['count'];
                }
            }
        }

        return [
            'is_connected'      => $isConnected,
            'gateway_status'    => $isConnected ? 'online' : 'offline',
            'gateway_name'      => $ping['data']['gateway'] ?? 'SiPintu REST API Gateway',
            'client_id'         => $this->clientId,
            'client_name'       => $ping['data']['client_connection']['name'] ?? 'TE-Vault',
            'total_requests'    => $ping['data']['client_connection']['total_api_requests'] ?? 0,
            'total_students'    => $studentCount,
            'total_teachers'    => $teacherCount,
            'latency_ms'        => $ping['data']['database']['latency_ms'] ?? 0.02,
            'last_connected_at' => $ping['data']['client_connection']['last_connected_at'] ?? now()->toIso8601String(),
        ];
    }

    /**
     * Safely store into cache without throwing exceptions.
     */
    protected function safeCachePut(string $key, mixed $value, int $ttl): void
    {
        try {
            $this->cacheStore()->put($key, $value, $ttl);
        } catch (\Throwable $e) {
            Log::warning('SiPintu cache put failed: ' . $e->getMessage());
        }
    }
}
