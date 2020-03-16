<?php declare(strict_types=1);

namespace Tests\CustomerGauge\Cognito\Fixtures;

use CustomerGauge\Cognito\Contracts\UserFactory;
use Illuminate\Contracts\Auth\Authenticatable;

final class MyUserFactory implements UserFactory
{
    public function make(array $payload): ?Authenticatable
    {
        $id = $payload['id'] ?? null;

        if ($id === 53) {
            return new MyUser;
        }

        $app = $payload['client_app'] ?? null;

        if ($app === '555') {
            return new MyUser;
        }

        return null;
    }
}
