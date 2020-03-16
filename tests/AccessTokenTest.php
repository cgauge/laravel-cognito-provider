<?php declare(strict_types=1);

namespace Tests\CustomerGauge\Cognito;

use CustomerGauge\Cognito\CognitoUserProvider;
use Tests\CustomerGauge\Cognito\Fixtures\MyUser;

final class AccessTokenTest extends TestCase
{
    public function test_access_token_will_be_converted_into_authenticatable_user()
    {
        $token = $this->jwtToken(['id' => 53]);

        $provider = $this->container->make(CognitoUserProvider::class);

        $auth = $provider->retrieveByCredentials(['cognito_token' => $token]);

        self::assertInstanceOf(MyUser::class, $auth);
    }

    public function test_user_factory_can_return_null()
    {
        $token = $this->jwtToken(['id' => 54]);

        $provider = $this->container->make(CognitoUserProvider::class);

        $auth = $provider->retrieveByCredentials(['cognito_token' => $token]);

        self::assertNull($auth);
    }
}
