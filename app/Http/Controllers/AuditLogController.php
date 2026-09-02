<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController extends Controller
{
    /**
     * API index endpoint.
     */
    public function index(): AnonymousResourceCollection
    {
        return AuditLogResource::collection(AuditLog::query()->with('actor')->latest('id')->paginate());
    }

    /**
     * Web view endpoint for Super Admin Audit Logs panel.
     */
    public function webIndex(Request $request)
    {
        $query = AuditLog::query()->with('actor')->latest('id');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->string('action') . '%');
        }

        if ($request->filled('search')) {
            $search = '%' . $request->string('search')->trim() . '%';
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', $search)
                  ->orWhere('entity_type', 'like', $search)
                  ->orWhereHas('actor', function ($aq) use ($search) {
                      $aq->where('name', 'like', $search)
                         ->orWhere('email', 'like', $search);
                  });
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }
}
