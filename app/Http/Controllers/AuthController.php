<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    /**
     * Gestisci una richiesta di login.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return \Laravel\Fortify\Contracts\LoginResponse
     */
    public function login(LoginRequest $request): LoginResponse
    {
        $request->validate([
            Fortify::username() => 'required|string',
            'password' => 'required|string',
        ]);

        return $this->loginPipeline($request)->then(function ($request) {
            return app(LoginResponse::class);
        });
    }

    /**
     * Ottieni la pipeline di login.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Pipeline\Pipeline
     */
    protected function loginPipeline(LoginRequest $request)
    {
        if (Features::enabled(Features::twoFactorAuthentication())) {
            return (new Pipeline(app()))
                ->send($request)
                ->through(array_filter([
                    EnsureLoginIsNotThrottled::class,
                    AttemptToAuthenticate::class,
                    RedirectIfTwoFactorAuthenticatable::class,
                    PrepareAuthenticatedSession::class,
                ]));
        }

        return (new Pipeline(app()))
            ->send($request)
            ->through(array_filter([
                EnsureLoginIsNotThrottled::class,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]));
    }

    /**
     * Gestisci una richiesta di logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LogoutResponse
     */
    public function logout(Request $request): LogoutResponse
    {
        return app(LogoutResponse::class);
    }
}
