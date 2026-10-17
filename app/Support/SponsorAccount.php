<?php

namespace App\Support;

use App\Models\Sponsor;
use App\Models\User;
use App\Notifications\SponsorInvitation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Throwable;

/**
 * Keeps a sponsor's dashboard account in step with their email address: an email opens an account straight away,
 * removing it closes the account. New accounts get a link to choose their own password, so no password is ever
 * typed or sent by the team.
 */
class SponsorAccount
{
    public const BROKER = 'sponsors';

    /**
     * Create, update or remove the account after the sponsor was saved. Returns the password link for a new account.
     */
    public static function sync(Sponsor $sponsor): ?string
    {
        $user = $sponsor->user;

        if (blank($sponsor->email)) {
            if ($user) {
                $sponsor->forceFill(['user_id' => null])->save();
                $user->delete();
            }

            return null;
        }

        if ($user) {
            $user->update(['name' => $sponsor->name, 'email' => $sponsor->email]);

            return null;
        }

        $user = User::create([
            'name' => $sponsor->name,
            'email' => $sponsor->email,
            'password' => Str::password(40),
            'role' => User::ROLE_SPONSOR,
        ]);
        $sponsor->forceFill(['user_id' => $user->id])->save();

        return self::invite($user);
    }

    /**
     * A fresh password link, also emailed to the sponsor. The team can share the same link over WhatsApp
     * in case the email does not arrive.
     */
    public static function invite(User $user): string
    {
        $link = route('password.reset', [
            'token' => Password::broker(self::BROKER)->createToken($user),
            'email' => $user->email,
        ]);

        try {
            $user->notify(new SponsorInvitation($link));
        } catch (Throwable $exception) {
            Log::warning('Sponsor invitation email could not be sent.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
        }

        return $link;
    }
}
