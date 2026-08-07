<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Core\Tenancy\TenantContext;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AuthenticatedSessionController
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'The provided credentials are invalid.']);
        }
        $request->session()->regenerate();
        /** @var User $user */ $user = $request->user();
        if (! $user->isActive() || $user->isTemporarilyLocked() || $user->isPasswordExpired()) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => 'This account is not available.']);
        }
        $tenant = app(TenantContext::class)->tenant();
        if ($tenant === null) {
            return redirect()->route('platform.setup');
        }
        $count = $user->memberships()->withoutGlobalScopes()->where('tenant_id', $tenant->id)->selectable()->count();
        if ($count === 0) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => 'No active membership is available for this tenant.']);
        }

        return redirect()->route('context.select');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
