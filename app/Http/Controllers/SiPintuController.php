<?php

namespace App\Http\Controllers;

use App\Models\GuruProfile;
use App\Services\SiPintuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiPintuController extends Controller
{
    public function __construct(
        protected SiPintuService $sipintu,
    ) {}

    /**
     * Halaman Utama Gateway SiPintu — Overview, status & monitoring.
     */
    public function index(Request $request)
    {
        $pingResult = $this->sipintu->ping();
        $summary = $this->sipintu->getDashboardSummary();
        $activeTab = $request->query('tab', 'overview');

        return view('sipintu.index', [
            'connection' => $pingResult,
            'summary'    => $summary,
            'activeTab'  => $activeTab,
        ]);
    }

    /**
     * Halaman Cek Data Pengguna / Siswa SIJUNA.
     */
    public function studentsPage(Request $request)
    {
        @ini_set('max_execution_time', 300);
        @set_time_limit(300);

        try {
            $search = $request->query('search', '');
            $nis = $request->query('nis', '');
            $studentsResult = $this->sipintu->getStudents(['search' => $search, 'nis' => $nis]);
            $pingResult = $this->sipintu->ping();

            return view('sipintu.students', [
                'students'   => $studentsResult,
                'connection' => $pingResult,
                'search'     => $search,
                'nis'        => $nis,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SiPintu studentsPage error: ' . $e->getMessage());

            return view('sipintu.students', [
                'students'   => [
                    'success' => false,
                    'total'   => 0,
                    'count'   => 0,
                    'data'    => [],
                    'message' => 'Gagal memuat data siswa dari SiPintu Gateway: ' . $e->getMessage(),
                ],
                'connection' => ['connected' => false, 'error' => $e->getMessage()],
                'search'     => $request->query('search', ''),
                'nis'        => $request->query('nis', ''),
            ])->with('error', 'Gagal memuat data dari SiPintu: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Cek Data Guru SIJUNA.
     */
    public function teachersPage(Request $request)
    {
        @ini_set('max_execution_time', 300);
        @set_time_limit(300);

        try {
            $search = $request->query('search', '');
            $nip = $request->query('nip', '');
            $teachersResult = $this->getEnrichedTeachers(['search' => $search, 'nip' => $nip]);
            $pingResult = $this->sipintu->ping();

            return view('sipintu.teachers', [
                'teachers'   => $teachersResult,
                'connection' => $pingResult,
                'search'     => $search,
                'nip'        => $nip,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SiPintu teachersPage error: ' . $e->getMessage());

            return view('sipintu.teachers', [
                'teachers'   => [
                    'success' => false,
                    'total'   => 0,
                    'count'   => 0,
                    'data'    => [],
                    'message' => 'Gagal memuat data guru dari SiPintu Gateway: ' . $e->getMessage(),
                ],
                'connection' => ['connected' => false, 'error' => $e->getMessage()],
                'search'     => $request->query('search', ''),
                'nip'        => $request->query('nip', ''),
            ])->with('error', 'Gagal memuat data dari SiPintu: ' . $e->getMessage());
        }
    }

    /**
     * AJAX Endpoint: Pencarian data siswa SIJUNA.
     */
    public function students(Request $request): JsonResponse
    {
        @ini_set('max_execution_time', 300);
        @set_time_limit(300);

        try {
            $params = $request->only(['nis', 'search']);
            $forceRefresh = $request->boolean('refresh', false);

            $result = $this->sipintu->getStudents($params, $forceRefresh);

            return response()->json($result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SiPintu students AJAX error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data siswa: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /**
     * AJAX Endpoint: Pencarian data guru SIJUNA.
     */
    public function teachers(Request $request): JsonResponse
    {
        @ini_set('max_execution_time', 300);
        @set_time_limit(300);

        try {
            $params = $request->only(['nip', 'search']);
            $forceRefresh = $request->boolean('refresh', false);

            $result = $this->getEnrichedTeachers($params, $forceRefresh);

            return response()->json($result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SiPintu teachers AJAX error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data guru: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Ambil data guru yang diselaraskan dan diperkaya dengan database lokal GuruProfile.
     * Memastikan NIP murni string (bukan numeric) agar tidak terpotong oleh IEEE 754 float JavaScript di browser.
     */
    protected function getEnrichedTeachers(array $params = [], bool $forceRefresh = false): array
    {
        $search = mb_strtolower(trim($params['search'] ?? ''));
        $nipFilter = trim($params['nip'] ?? '');

        // 1. Ambil data guru dari SiPintu Gateway / cache
        try {
            $teachersResult = $this->sipintu->getTeachers($params, $forceRefresh);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('SiPintu getTeachers error: ' . $e->getMessage());
            $teachersResult = [
                'success' => false,
                'total'   => 0,
                'count'   => 0,
                'data'    => [],
                'source'  => 'SiPintu Gateway (Error)',
            ];
        }

        // 2. Ambil data profil guru lokal
        try {
            $localProfiles = GuruProfile::with('user')->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('SiPintu load local profiles error: ' . $e->getMessage());
            $localProfiles = collect();
        }

        $localByNip = $localProfiles->filter(fn($p) => !empty($p->nip))->keyBy(fn($p) => (string) $p->nip);
        $localByCode = $localProfiles->filter(fn($p) => !empty($p->code))->keyBy(fn($p) => strtoupper(trim((string) $p->code)));
        $localByEmail = $localProfiles->filter(fn($p) => !empty($p->user?->email))->keyBy(fn($p) => mb_strtolower(trim((string) $p->user->email)));
        $localByName = $localProfiles->filter(fn($p) => !empty($p->user?->name))->keyBy(fn($p) => mb_strtolower(trim((string) $p->user->name)));

        // 3. Jika Gateway menyediakan data, selaraskan dengan database lokal
        if (!empty($teachersResult['data']) && is_array($teachersResult['data'])) {
            foreach ($teachersResult['data'] as &$item) {
                $rawNip = $item['nip'] ?? '';
                if (is_float($rawNip) || (is_numeric($rawNip) && str_contains((string) $rawNip, 'E'))) {
                    $itemNip = number_format((float) $rawNip, 0, '', '');
                } else {
                    $itemNip = trim((string) $rawNip);
                }

                $itemKode = strtoupper(trim((string) ($item['kode'] ?? '')));
                $itemEmail = mb_strtolower(trim((string) ($item['user']['email'] ?? '')));
                $itemNama = mb_strtolower(trim((string) ($item['nama'] ?? '')));

                // Cocokkan dengan data lokal (prioritas: nip -> kode guru -> email sijuna -> nama)
                $matched = null;
                if ($itemNip !== '' && $localByNip->has($itemNip)) {
                    $matched = $localByNip->get($itemNip);
                } elseif ($itemKode !== '' && $itemKode !== '-' && $localByCode->has($itemKode)) {
                    $matched = $localByCode->get($itemKode);
                } elseif ($itemEmail !== '' && $localByEmail->has($itemEmail)) {
                    $matched = $localByEmail->get($itemEmail);
                } elseif ($itemNama !== '' && $localByName->has($itemNama)) {
                    $matched = $localByName->get($itemNama);
                }

                if ($matched) {
                    // Selaraskan dengan data lokal terverifikasi (NIP murni string)
                    $item['nip'] = (string) $matched->nip;
                    if (!empty($matched->code)) {
                        $item['kode'] = (string) $matched->code;
                    }
                    if (!empty($matched->phone)) {
                        $item['hp'] = (string) $matched->phone;
                    }
                    if ($matched->user) {
                        $item['user']['name'] = (string) $matched->user->name;
                        $item['user']['email'] = (string) $matched->user->email;
                        if (empty($item['nama']) || $item['nama'] === '-') {
                            $item['nama'] = (string) $matched->user->name;
                        }
                    }
                } else {
                    $item['nip'] = $itemNip ?: '-';
                }
            }
            unset($item);

            return $teachersResult;
        }

        // 4. Fallback ke database lokal jika data Gateway offline atau kosong
        if ($localProfiles->isNotEmpty()) {
            $items = [];
            foreach ($localProfiles as $lp) {
                $nipStr = (string) ($lp->nip ?? '-');
                $kodeStr = (string) ($lp->code ?? '-');
                $namaStr = (string) ($lp->user?->name ?? '-');
                $emailStr = (string) ($lp->user?->email ?? '-');
                $hpStr = (string) ($lp->phone ?? '-');

                if ($nipFilter !== '' && !str_contains($nipStr, $nipFilter) && !str_contains($kodeStr, $nipFilter)) {
                    continue;
                }
                if ($search !== '') {
                    $matchSearch = str_contains(mb_strtolower($namaStr), $search)
                        || str_contains($nipStr, $search)
                        || str_contains(mb_strtolower($kodeStr), $search)
                        || str_contains(mb_strtolower($emailStr), $search)
                        || str_contains($hpStr, $search);
                    if (!$matchSearch) {
                        continue;
                    }
                }

                $items[] = [
                    'id'             => $lp->id,
                    'nip'            => $nipStr,
                    'kode'           => $kodeStr,
                    'nama'           => $namaStr,
                    'nama_panggilan' => null,
                    'jk'             => null,
                    'hp'             => $hpStr,
                    'alamat'         => '-',
                    'status'         => 1,
                    'user'           => [
                        'email' => $emailStr,
                        'name'  => $namaStr,
                    ],
                ];
            }

            return [
                'success'     => true,
                'total'       => count($localProfiles),
                'count'       => count($items),
                'data'        => array_values($items),
                'source'      => 'Database Lokal SITEFA (Sinkron)',
                'is_filtered' => ($search !== '' || $nipFilter !== ''),
            ];
        }

        return $teachersResult;
    }

    /**
     * AJAX Endpoint: Cek status koneksi ke SiPintu Gateway.
     */
    public function connectionStatus(Request $request): JsonResponse
    {
        $forceRefresh = $request->boolean('refresh', false);
        $ping = $this->sipintu->ping($forceRefresh);

        return response()->json($ping);
    }
}
