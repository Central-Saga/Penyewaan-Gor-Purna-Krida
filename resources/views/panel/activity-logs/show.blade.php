@php
    $title = 'Detail Log Aktivitas';

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

    [$eventLabel, $eventColor] = $eventLabels[$log->event] ?? [ucfirst((string) $log->event), 'secondary'];
    $subject = rescue(fn () => $log->subject, null, false);
    $subjectText = $subject?->kode ?? $subject?->nama ?? $subject?->name ?? ($subject ? $subject->getKey() : null);
@endphp
<x-layouts::app.sidebar :title="$title">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">Detail Log Aktivitas</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('activity.logs.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Timestamp</label>
                        <p class="mb-0">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Pengguna</label>
                        <p class="mb-0">
                            @if($log->causer)
                                <span class="fw-semibold">{{ $log->causer->name }}</span>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="text-muted small">Log</label>
                        <p class="mb-0"><span class="badge text-bg-info">{{ ucfirst((string) $log->log_name) }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Event</label>
                        <p class="mb-0"><span class="badge text-bg-{{ $eventColor }}">{{ $eventLabel }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Deskripsi</label>
                        <p class="mb-0">{{ $log->description ?: '—' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="text-muted small">Model</label>
                        <p class="mb-0">{{ $modelLabels[$log->subject_type] ?? ($log->subject_type ? class_basename($log->subject_type) : '—') }}</p>
                    </div>
                    <div class="col-md-8">
                        <label class="text-muted small">Subject</label>
                        <p class="mb-0">{{ $subjectText ?? '—' }}</p>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Properties</h5>
                @if(count($log->properties->toArray() ?: []) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Key</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($log->properties->toArray() as $key => $value)
                                    <tr>
                                        <td><code>{{ $key }}</code></td>
                                        <td>
                                            @if(is_array($value))
                                                <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Tidak ada properti yang tercatat pada aktivitas ini.</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app.sidebar>
