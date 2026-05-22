<?php

use App\Models\User;

test('guest is redirected to login from dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('verified user can view dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});
