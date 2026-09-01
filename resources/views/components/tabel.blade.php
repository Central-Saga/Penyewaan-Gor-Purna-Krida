@props([
    'paginator',
    'columns',
])

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                @foreach ($columns as $column)
                    <th scope="col">{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

@if (method_exists($paginator, 'hasPages') && $paginator->hasPages())
    <nav aria-label="Paginasi">
        {{ $paginator->links() }}
    </nav>
@endif