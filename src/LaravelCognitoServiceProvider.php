<?php

declare(strict_types=1);

namespace CustomerGauge\Cognito;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

final class LaravelCognitoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerIssuer();

        $this->registerCognitoUserProvider();
    }

    private function registerIssuer(): void
    {
        $this->app->bind(Issuer::class, function () {
            $config = $this->app->get('config');

            $pool = $config->get('auth.cognito.pool');

            $region = $config->get('auth.cognito.region');

            return new Issuer($pool, $region);
        });
    }

    private function registerCognitoUserProvider(): void
    {
        Auth::provider(CognitoUserProvider::class, static function (Container $app) {
            return $app->make(CognitoUserProvider::class);
        });
    }
}
