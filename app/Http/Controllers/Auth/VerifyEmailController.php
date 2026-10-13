<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(
        EmailVerificationRequest $request
    ): RedirectResponse {
        $user = $request->user();

        if (!$user->hasVerifiedEmail()) {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
        }

        // Если пользователь пришёл по приглашению в команду,
        // возвращаем его к исходной подписанной ссылке.
        $inviteToken = $request->session()->get('company_invite_token');

if ($inviteToken) {
    return redirect()->route('team.invitation.continue', [
        'token' => $inviteToken,
    ]);
}

        return redirect()->intended(
            RouteServiceProvider::HOME . '?verified=1'
        );
    }
}
