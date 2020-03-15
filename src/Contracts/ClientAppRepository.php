<?php declare(strict_types=1);

namespace CustomerGauge\Cognito\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface ClientAppRepository
{
    public function find(array $payload): Authenticatable;
}
