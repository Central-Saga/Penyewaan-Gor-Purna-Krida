<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * List all activity logs.
     */
    public function index(Request $request)
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
    public function show(Activity $log)
    {
        return view('panel.activity-logs.show');
    }

    /**
     * Export activity logs to CSV.
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $query = Activity::with('causer')
            ->when($request->filled('start_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->orderBy('created_at', 'desc');

        $logs = $query->get();

        $handle = tmpfile();
        fputcsv($handle, ['Timestamp', 'User', 'Action', 'Model', 'Properties']);
        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->causer?->name ?? 'System',
                $log->log_name,
                $log->subject_type ?? '-',
                json_encode($log->properties->toArray() ?: []),
            ]);
        }
        rewind($handle);

        return response(stream_get_contents($handle))
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="activity_logs_'.date('Y-m-d').'.csv"');
    }
}
