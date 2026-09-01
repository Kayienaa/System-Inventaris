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
    }

    /**
     * Halaman Cek Data Guru SIJUNA.
     */
    public function teachersPage(Request $request)
    {
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
    }

    /**
     * AJAX Endpoint: Pencarian data siswa SIJUNA.
     */
    public function students(Request $request): JsonResponse
    {
        $params = $request->only(['nis', 'search']);
        $forceRefresh = $request->boolean('refresh', false);

        $result = $this->sipintu->getStudents($params, $forceRefresh);

        return response()->json($result);
    }

    /**
     * AJAX Endpoint: Pencarian data guru SIJUNA.
     */
    public function teachers(Request $request): JsonResponse
    {
        $params = $request->only(['nip', 'search']);
        $forceRefresh = $request->boolean('refresh', false);

        $result = $this->sipintu->getTeachers($params, $forceRefresh);

        return response()->json($result);
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
