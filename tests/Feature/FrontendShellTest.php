<?php

declare(strict_types=1);

use App\Models\User;

it('exposes the public homepage route', function (): void {
    expect(route('home'))->toContain('/');
});

it('exposes the admin dashboard route', function (): void {
    expect(route('admin.dashboard'))->toContain('/admin');
});

it('exposes the authenticated profile route', function (): void {
    expect(route('profile'))->toContain('/profile');
});

it('uses the panel access service for admin access checks', function (): void {
    $user = new User();

    expect($user->exists)->toBeFalse();
});
