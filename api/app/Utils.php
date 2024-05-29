<?php

namespace App;

use Illuminate\Support\Facades\Password;

class Utils
{
    public static function sendPasswordReset(string $email)
    {
        $status = Password::broker('members')->sendResetLink(
            ['email' => $email]
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}
