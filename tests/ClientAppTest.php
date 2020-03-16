<?php declare(strict_types=1);

namespace Tests\CustomerGauge\Cognito;

use CustomerGauge\Cognito\CognitoUserProvider;
use Tests\CustomerGauge\Cognito\Fixtures\MyUser;

final class ClientAppTest extends TestCase
{
    public function test_client_app_will_be_converted_into_authenticatable_user()
    {
        $token = $this->jwtToken(['client_app' => '555']);

        $provider = $this->container->make(CognitoUserProvider::class);

        $auth = $provider->retrieveByCredentials(['cognito_token' => $token]);

        self::assertInstanceOf(MyUser::class, $auth);
    }

    public function test_invalid_token_will_return_null()
    {
        $provider = $this->container->make(CognitoUserProvider::class);

        $auth = $provider->retrieveByCredentials(['cognito_token' => 'invalid']);

        self::assertNull($auth);
    }
}
