<?php

use App\Models\User;
use Spatie\Activitylog\Models\Activity;

test('admin can view activity logs index', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('activity.logs.index'))
        ->assertOk();
});

test('admin can export activity logs as csv', function () {
    $admin = User::factory()->admin()->create();

    // Create a sample activity log
    Activity::create([
        'log_name' => 'peminjaman',
        'description' => 'Test activity',
        'subject_type' => 'Peminjaman',
        'causer_id' => 1,
        'properties' => [],
        'event' => 'updated',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('activity.logs.export', ['start_date' => '2026-01-01']));

    $response->assertOk();
    $this->assertStringContainsString('Timestamp,User,Action,Model,Properties', $response->getContent());
});

test('pengguna biasa tidak bisa melihat activity log', function () {
    $user = User::factory()->create(); // Regular user without admin/pengelola role

    $this->actingAs($user)
        ->get(route('activity.logs.index'))
        ->assertForbidden();
});
