<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateEvents;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirects user to social provider
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($provider): \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handles callback from social provider
     * @param $provider
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function callback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        if($socialUser) {
            $user = User::updateOrCreate([
                'provider_user_id' => $socialUser->id,
                'provider_id' => $provider,
            ], [
                'name' => $socialUser->name ?: $socialUser->nickname,
                'email' => $socialUser->email,
                'password' => Hash::make(Str::random(20))
            ]);

            Auth::login($user);

            // Seed events for user
            GenerateEvents::dispatch($user);
        }

        return redirect('/spa');
    }
}
