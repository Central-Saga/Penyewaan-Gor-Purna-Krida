@props([
    'title',
    'description',
])

<div class="text-center mb-4">
    <h1 class="h4 fw-bold">{{ $title }}</h1>
    <p class="text-secondary mb-0">{{ $description }}</p>
</div>