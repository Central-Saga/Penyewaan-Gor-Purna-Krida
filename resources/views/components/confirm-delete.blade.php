@props([
    'action' => null,
    'title' => 'Konfirmasi Hapus',
    'body' => 'Data yang dihapus tidak dapat dikembalikan. Lanjutkan?',
    'label' => 'Hapus',
])

<button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
        data-bs-target="#modal-{{ md5($action.$label) }}">
    {{ $label }}
</button>

<div class="modal fade" id="modal-{{ md5($action.$label) }}" tabindex="-1"
     aria-labelledby="modal-{{ md5($action.$label) }}-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-{{ md5($action.$label) }}-label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                {{ $body }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>

                @if ($action)
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Hapus') }}</button>
                    </form>
                @else
                    <button type="button" class="btn btn-danger" {{ $attributes }}
                            data-bs-dismiss="modal">{{ __('Hapus') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>