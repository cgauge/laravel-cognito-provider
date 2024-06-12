<?php

declare(strict_types=1);

namespace CustomerGauge\Cognito\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserFactory
{
    /** @param mixed[] $payload */
    public function make(array $payload): Authenticatable|null;
}
