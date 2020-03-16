<?php declare(strict_types=1);

namespace Tests\CustomerGauge\Cognito;

use CustomerGauge\Cognito\CognitoUserProvider;
use CustomerGauge\Cognito\Contracts\ClientAppRepository;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
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

class FakeClientAppRepository implements ClientAppRepository
{
    public function find(array $payload): Authenticatable
    {
        if ($payload['client_app'] === '555') {
            return new MyUser;
        }

        throw new Exception();
    }
}
