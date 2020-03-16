<?php declare(strict_types=1);

namespace CustomerGauge\Cognito\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserFactory
{
    public function fromAccessToken(array $payload): Authenticatable;
}