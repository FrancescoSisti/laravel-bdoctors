<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        'XSRF-TOKEN'
    ];

    /**
     * Define the cookie domain
     *
     * @return string|null
     */
    protected function cookieDomain()
    {
        return config('session.domain');
    }
}
