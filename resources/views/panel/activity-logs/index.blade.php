@php
    $title = 'Log Aktivitas';
@endphp
<x-layouts::app.sidebar :title="$title">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">Log Aktivitas</h1>
                <p class="text-muted">Daftar semua aktivitas sistem yang dilakukan oleh pengguna.</p>
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
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary me-2">
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
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="180">Timestamp</th>
                                <th width="120">Pengguna</th>
                                <th width="100">Log</th>
                                <th width="60">Event</th>
                                <th width="100">Model</th>
                                <th>Subject</th>
                                <th>Properties</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($query as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $log->causer?->name ?? 'System' }}</td>
                                    <td><span class="badge bg-info">{{ $log->log_name }}</span></td>
                                    <td><span class="badge bg-secondary">{{ $log->event }}</span></td>
                                    <td>{{ $log->subject_type ?? '-' }}</td>
                                    <td>{{ $log->subject?->kode ?? $log->subject?->nama ?? '-' }}</td>
                                    <td>
                                        @php
                                            $keys = array_keys($log->properties->toArray() ?: []);
                                        @endphp
                                        <small class="text-muted">
                                            {{ count($keys) }} properti
                                        </small>
                                    </td>
                                    <td>
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
                    <div class="mt-3">
                        {{ $query->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app.sidebar>
