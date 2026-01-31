<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    // Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            return $this->loginOrCreateUser($googleUser, 'google');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google login failed');
        }
    }

    // LinkedIn
    public function redirectToLinkedIn()
    {
        return Socialite::driver('linkedin')->redirect();
    }

    public function handleLinkedInCallback()
    {
        try {
            $linkedinUser = Socialite::driver('linkedin')->stateless()->user();
            return $this->loginOrCreateUser($linkedinUser, 'linkedin');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'LinkedIn login failed');
        }
    }

    // Общий метод для входа или создания пользователя
    protected function loginOrCreateUser($socialUser, $provider)
    {
        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'password' => bcrypt(uniqid()), // случайный пароль
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ]
        );

        Auth::login($user, true);

        return redirect()->intended('/dashboard');
    }
}
