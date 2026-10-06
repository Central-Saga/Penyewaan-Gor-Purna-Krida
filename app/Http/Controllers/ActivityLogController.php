<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * List all activity logs.
     */
    public function index(Request $request): View
    {
        $query = Activity::with('causer')
            ->when($request->filled('start_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('panel.activity-logs.index', compact('query'));
    }

    /**
     * Show a specific activity log.
     */
    public function show(Activity $log): View
    {
        return view('panel.activity-logs.show', compact('log'));
    }

    /**
     * Export activity logs to CSV.
     */
    public function export(Request $request): Response
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $query = Activity::with('causer')
            ->when($request->filled('start_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->orderBy('created_at', 'desc');

        $logs = $query->get();

        $modelLabels = [
            'App\Models\User' => 'Pengguna',
            'App\Models\Fasilitas' => 'Fasilitas',
            'App\Models\Peminjaman' => 'Peminjaman',
            'App\Models\PeminjamanLog' => 'Log Peminjaman',
        ];

        $handle = tmpfile();
        fputcsv($handle, ['Timestamp', 'Pengguna', 'Log', 'Model', 'Properties']);
        foreach ($logs as $log) {
            $causer = $log->causer;
            $model = $log->subject_type
                ? ($modelLabels[$log->subject_type] ?? class_basename($log->subject_type))
                : '-';

            fputcsv($handle, [
                $log->created_at->format('Y-m-d H:i:s'),
                $causer === null ? 'System' : (string) $causer->getAttribute('name'),
                $log->log_name,
                $model,
                json_encode($log->properties->toArray() ?: []),
            ]);
        }
        rewind($handle);

        return response(stream_get_contents($handle))
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="activity_logs_'.date('Y-m-d').'.csv"');
    }
}
