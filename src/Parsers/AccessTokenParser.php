<?php declare(strict_types=1);

namespace CustomerGauge\Cognito\Parsers;

use CustomerGauge\Cognito\Contracts\UserFactory;
use CustomerGauge\Cognito\TokenVerifier;
use Illuminate\Contracts\Auth\Authenticatable;

final class AccessTokenParser
{
    private $verifier;

    private $factory;

    public function __construct(TokenVerifier $verifier, UserFactory $factory)
    {
        $this->verifier = $verifier;
        $this->factory = $factory;
    }

    public function parse(string $token): Authenticatable
    {
        $payload = $this->verifier->verify($token);

        return $this->factory->fromAccessToken($payload);
    }
}
