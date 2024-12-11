<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Braintree\Gateway;

class BraintreeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Gateway::class, function ($app) {
            return new Gateway([
                'environment' => config('braintree.environment'),
                'merchantId' => config('braintree.merchant_id'),
                'publicKey' => config('braintree.public_key'),
                'privateKey' => config('braintree.private_key')
            ]);
        });
    }

    public function boot()
    {
        //
    }
}