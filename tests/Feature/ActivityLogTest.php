<?php

use App\Models\Peminjaman;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

test('admin can view activity logs index', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('activity.logs.index'))
        ->assertOk();
});

test('admin dapat melihat detail log aktivitas dengan subject yang dapat diresolusi', function () {
    $admin = User::factory()->admin()->create();
    $peminjaman = Peminjaman::factory()->create();

    $log = Activity::create([
        'log_name' => 'peminjaman',
        'description' => 'Status berubah',
        'subject_type' => $peminjaman->getMorphClass(),
        'subject_id' => $peminjaman->id,
        'causer_type' => $admin->getMorphClass(),
        'causer_id' => $admin->id,
        'properties' => [],
        'event' => 'updated',
    ]);

    $this->actingAs($admin)
        ->get(route('activity.logs.index'))
        ->assertOk()
        ->assertSee($peminjaman->kode);

    $this->actingAs($admin)
        ->get(route('activity.logs.show', $log))
        ->assertOk();
});

test('index dan detail log tidak error saat subject_type tidak dapat diresolusi', function () {
    $admin = User::factory()->admin()->create();

    // Data legacy: subject_type tanpa namespace (tidak dapat diresolusi morphTo).
    $log = Activity::create([
        'log_name' => 'peminjaman',
        'description' => 'Data legacy',
        'subject_type' => 'Peminjaman',
        'subject_id' => 999,
        'properties' => [],
        'event' => 'updated',
    ]);

    $this->actingAs($admin)
        ->get(route('activity.logs.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('activity.logs.show', $log))
        ->assertOk();
});

test('admin can export activity logs as csv', function () {
    $admin = User::factory()->admin()->create();

    // Create a sample activity log
    Activity::create([
        'log_name' => 'peminjaman',
        'description' => 'Test activity',
        'subject_type' => 'App\Models\Peminjaman',
        'causer_id' => 1,
        'properties' => [],
        'event' => 'updated',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('activity.logs.export', ['start_date' => '2026-01-01']));

    $response->assertOk();
    $this->assertStringContainsString('Timestamp,Pengguna,Log,Model,Properties', $response->getContent());
});

test('pengguna biasa tidak bisa melihat activity log', function () {
    $user = User::factory()->create(); // Regular user without admin/pengelola role

    $this->actingAs($user)
        ->get(route('activity.logs.index'))
        ->assertForbidden();
});
