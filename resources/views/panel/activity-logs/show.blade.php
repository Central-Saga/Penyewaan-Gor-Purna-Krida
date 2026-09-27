@php
    $title = 'Detail Log Aktivitas';
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
                        <p class="mb-0">{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">User</label>
                        <p class="mb-0">{{ $log->causer?->name ?? 'System' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="text-muted small">Log Name</label>
                        <p class="mb-0"><span class="badge bg-info">{{ $log->log_name }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Event</label>
                        <p class="mb-0"><span class="badge bg-secondary">{{ $log->event }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Description</label>
                        <p class="mb-0">{{ $log->description ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="text-muted small">Subject Type</label>
                        <p class="mb-0">{{ $log->subject_type ?? '-' }}</p>
                    </div>
                    <div class="col-md-8">
                        <label class="text-muted small">Subject</label>
                        <p class="mb-0">{{ $log->subject ? ($log->subject->getKey()) : '-' }}</p>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Properties</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
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
                                            <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app.sidebar>
