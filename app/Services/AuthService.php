<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Sanctum SPA session auth. No Repository layer here — Auth::attempt()
 * (login) and the single User::create() call (register) already are the
 * query/business-logic boundary for this class, so an extra repository
 * would just wrap them with nothing to add.
 */
class AuthService
{
    /**
     * Registers a new admin user and logs them in immediately, matching
     * login()'s session behavior — there's no separate "verify then log
     * in" step for this single-admin app.
     */
    public function register(string $name, string $email, string $password): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        // `role` has a DB-level default (see the add_role_to_users_table
        // migration) that create()'s in-memory model never picks up on its
        // own — without this, the register response reports role: null
        // even though the row itself is correctly "admin".
        $user->refresh();

        Auth::login($user);
        request()->session()->regenerate();

        return $user;
    }

    public function login(string $email, string $password): User
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        request()->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
