<?php declare(strict_types=1);

namespace CustomerGauge\Cognito;

use CustomerGauge\Cognito\Parsers\AccessTokenParser;
use CustomerGauge\Cognito\Parsers\ClientAppParser;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

final class CognitoUserProvider implements UserProvider
{
    private $clientAppParser;

    private $accessTokenParser;

    public function __construct(ClientAppParser $clientAppParser, AccessTokenParser $accessTokenParser)
    {
        $this->clientAppParser = $clientAppParser;
        $this->accessTokenParser = $accessTokenParser;
    }

    public function retrieveByCredentials(array $credentials)
    {
        $token = $credentials['cognito_token'];

        try {
            return $this->clientAppParser->parse($token);
        } catch (Exception $e) {
            // If we cannot parse the token, that probably means the token is either invalid or
            // it might be an access token. We'll try to validate it as an access token next
            // and if that fails, we'll finally return null.
        }

        try {
            return $this->accessTokenParser->parse($token);
        } catch (Exception $e) {
            // The Laravel Authenticate Middleware implements the Chain Of Responsibility Pattern.
            //  We need to return null and let the next Guard try to authenticate. If all Guards
            // return null, then the middleware throws an AuthenticationException.
        }

        return null;
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
}
