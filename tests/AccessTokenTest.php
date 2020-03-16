<?php declare(strict_types=1);

namespace Tests\CustomerGauge\Cognito;

use CustomerGauge\Cognito\CognitoUserProvider;
use CustomerGauge\Cognito\Contracts\UserFactory;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
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
}

class FakeUserFactory implements UserFactory
{
    public function fromAccessToken(array $payload): Authenticatable
    {
        if ($payload['id'] === 53) {
            return new MyUser;
        }

        throw new Exception;
    }
}
