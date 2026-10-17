<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\SponsorAccount;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Where a sponsor lands from their invitation link to choose a password for their new account.
 */
class PasswordSetupController extends Controller
{
    public function create(Request $request, string $token): View
    {
        return view('auth.set-password', ['token' => $token, 'email' => (string) $request->query('email')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ]);

        $status = Password::broker(SponsorAccount::BROKER)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))->withErrors(['email' => __('sponsors.password_link_invalid')]);
        }

        return redirect()->route('login')->with('status', __('sponsors.password_set'));
    }
}
