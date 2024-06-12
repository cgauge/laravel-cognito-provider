<?php declare(strict_types=1);

namespace Tests\CustomerGauge\Cognito\Fixtures;

use Illuminate\Contracts\Auth\Authenticatable;

class MyUser implements Authenticatable
{
    public function getAuthIdentifierName()
    {
    }

    public function getAuthIdentifier()
    {
    }

    public function getAuthPassword()
    {
    }

    public function getRememberToken()
    {
    }

    public function setRememberToken($value)
    {
    }

    public function getRememberTokenName()
    {
    }

    public function getAuthPasswordName()
    {   
    }
}
