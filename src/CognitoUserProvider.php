<?php

declare(strict_types=1);

namespace CustomerGauge\Cognito;

use BadMethodCallException;
use CustomerGauge\Cognito\Contracts\UserFactory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Throwable;

final class CognitoUserProvider implements UserProvider
{
    public function __construct(private TokenParser $parser, private UserFactory $factory)
    {
    }

    /** @inheritdoc */
    public function retrieveByCredentials(array $credentials)
    {
        $token = $credentials['cognito_token'];

        try {
            $payload = $this->parser->parse($token);
        } catch (Throwable) {
            // If we cannot parse the token, that probably means it's an invalid Token. Since
            // the Authenticate Middleware implements a Chain Of Responsibility Pattern,
            // we have to return null so that other Guards can try to authenticate.
            return null;
        }

        return $this->factory->make($payload);
    }

    /**
     * @inheritdoc
     * @phpstan ignore
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * @inheritdoc
     * @phpstan ignore
     */
    public function retrieveById($identifier)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * @inheritdoc
     * @phpstan ignore
     */
    public function retrieveByToken($identifier, $token)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * @inheritdoc
     * @phpstan ignore
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * @inheritdoc
     * @phpstan ignore
     */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        throw new BadMethodCallException('Not implemented');
    }
}
