<?php declare(strict_types=1);

namespace CustomerGauge\Cognito;

use CustomerGauge\Cognito\Contracts\UserFactory;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

final class CognitoUserProvider implements UserProvider
{
    private $parser;

    private $factory;

    public function __construct(TokenParser $parser, UserFactory $factory)
    {
        $this->parser = $parser;
        $this->factory = $factory;
    }

    public function retrieveByCredentials(array $credentials)
    {
        $token = $credentials['cognito_token'];

        try {
            $payload = $this->parser->parse($token);

        } catch (Exception $e) {
            // If we cannot parse the token, that probably means it's an invalid Token. Since
            // the Authenticate Middleware implements a Chain Of Responsibility Pattern,
            // we have to return null so that other Guards can try to authenticate.
            return null;
        }

        return $this->factory->make($payload);
    }

    /** @phpstan ignore */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
    }

    /** @phpstan ignore */
    public function retrieveById($identifier)
    {
    }

    /** @phpstan ignore */
    public function retrieveByToken($identifier, $token)
    {
    }

    /** @phpstan ignore */
    public function updateRememberToken(Authenticatable $user, $token)
    {
    }

    /** @phpstan-ignore-next-line */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
    }
}
