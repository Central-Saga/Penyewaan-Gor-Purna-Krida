@php
    $title = 'Log Aktivitas';

    $modelLabels = [
        'App\Models\User' => 'Pengguna',
        'App\Models\Fasilitas' => 'Fasilitas',
        'App\Models\Peminjaman' => 'Peminjaman',
        'App\Models\PeminjamanLog' => 'Log Peminjaman',
    ];

    $eventLabels = [
        'created' => ['Dibuat', 'success'],
        'updated' => ['Diperbarui', 'warning'],
        'deleted' => ['Dihapus', 'danger'],
        'restored' => ['Dipulihkan', 'info'],
        'logged-in' => ['Masuk', 'info'],
        'logged-out' => ['Keluar', 'secondary'],
    ];

    $subjectLabel = function ($log) {
        $subject = rescue(fn () => $log->subject, null, false);

        return $subject?->kode
            ?? $subject?->nama
            ?? $subject?->name
            ?? ($subject ? $subject->getKey() : null);
    };
@endphp
<x-layouts::app.sidebar :title="$title">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">Log Aktivitas</h1>
                <p class="text-muted mb-0">Daftar semua aktivitas sistem yang dilakukan oleh pengguna.</p>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('activity.logs.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="startDate" class="form-label">Tanggal Mulai</label>
                            <input type="date" id="startDate" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate" class="form-label">Tanggal Akhir</label>
                            <input type="date" id="endDate" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2 pb-1">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('activity.logs.export', array_filter(['start_date' => request('start_date'), 'end_date' => request('end_date')])) }}" class="btn btn-success">
                                <i class="bi bi-file-earmark-csv"></i> Export CSV
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="170">Timestamp</th>
                                <th width="150">Pengguna</th>
                                <th width="110">Log</th>
                                <th width="120">Event</th>
                                <th width="130">Model</th>
                                <th>Subject</th>
                                <th width="110">Properties</th>
                                <th width="90" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($query as $log)
                                @php
                                    [$eventLabel, $eventColor] = $eventLabels[$log->event] ?? [ucfirst((string) $log->event), 'secondary'];
                                    $subjectText = $subjectLabel($log);
                                    $propCount = count($log->properties->toArray() ?: []);
                                @endphp
                                <tr>
                                    <td class="text-nowrap">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</td>
                                    <td>
                                        @if($log->causer)
                                            <span class="fw-semibold">{{ $log->causer->name }}</span>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td><span class="badge text-bg-info">{{ ucfirst((string) $log->log_name) }}</span></td>
                                    <td><span class="badge text-bg-{{ $eventColor }}">{{ $eventLabel }}</span></td>
                                    <td>{{ $modelLabels[$log->subject_type] ?? ($log->subject_type ? class_basename($log->subject_type) : '—') }}</td>
                                    <td>{{ $subjectText ?? '—' }}</td>
                                    <td>
                                        @if($propCount > 0)
                                            <span class="text-muted">{{ $propCount }} properti</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('activity.logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Tidak ada log aktivitas yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($query->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $query->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app.sidebar>
