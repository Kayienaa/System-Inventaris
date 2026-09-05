<?php

namespace App\Http\Controllers;

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
            $teachersResult = $this->sipintu->getTeachers(['search' => $search, 'nip' => $nip]);
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

            $result = $this->sipintu->getTeachers($params, $forceRefresh);

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
     * AJAX Endpoint: Cek status koneksi ke SiPintu Gateway.
     */
    public function connectionStatus(Request $request): JsonResponse
    {
        $forceRefresh = $request->boolean('refresh', false);
        $ping = $this->sipintu->ping($forceRefresh);

        return response()->json($ping);
    }
}
