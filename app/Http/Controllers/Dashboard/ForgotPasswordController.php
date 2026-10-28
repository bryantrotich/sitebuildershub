<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Dashboard\ResetPasswordNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function create(): Response
    {
        return Inertia::render('Dashboard/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Email a password reset link to a staff member.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Only staff accounts can reset their password from the dashboard.
        $isStaff = User::where('email', $request->email)
            ->whereHas('role', fn ($query) => $query->where('state', '>', 0))
            ->exists();

        $status = $isStaff
            ? Password::sendResetLink(
                $request->only('email'),
                fn (User $user, string $token) => $user->notify(new ResetPasswordNotification($token))
            )
            : Password::RESET_LINK_SENT;

        if ($status == Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }

        // Same message whether or not the account exists, so emails cannot be probed.
        return back()->with('status', 'If that email belongs to a staff account, a password reset link has been sent to it.');
    }

    /**
     * Display the form to choose a new password.
     */
    public function edit(Request $request, string $token): Response
    {
        return Inertia::render('Dashboard/ResetPassword', [
            'email' => $request->query('email'),
            'token' => $token,
        ]);
    }

    /**
     * Save the new password.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password'          => Hash::make($request->password),
                    'remember_token'    => Str::random(60),
                    // Using the emailed link proves the address, which matters for invited staff.
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('dashboard.login')->with('status', 'Your password has been reset. You can now log in.');
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
