<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_audit_logs');

        $query = Activity::with(['causer', 'subject']);

        // Filter by Event Type (created, updated, deleted, impersonated, etc.)
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by Causer / User
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by Search Query (description, log_name, subject_type)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('log_name', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Metrics for Audit Header
        $totalLogs = Activity::count();
        $todayLogs = Activity::whereDate('created_at', today())->count();
        $createdLogs = Activity::where('event', 'created')->count();
        $updatedLogs = Activity::where('event', 'updated')->count();
        $deletedLogs = Activity::where('event', 'deleted')->count();

        $causers = User::whereHas('activities')->orderBy('name')->get();

        return view('admin.audit.index', compact(
            'activities',
            'totalLogs',
            'todayLogs',
            'createdLogs',
            'updatedLogs',
            'deletedLogs',
            'causers'
        ));
    }
}
