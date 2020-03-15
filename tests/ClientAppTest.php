<?php declare(strict_types=1);

namespace Tests\CustomerGauge\Cognito;

use CustomerGauge\Cognito\CognitoUserProvider;
use CustomerGauge\Cognito\Contracts\ClientAppRepository;
use CustomerGauge\Cognito\Issuer;
use Exception;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Cache\Repository as RepositoryContract;
use Jose\Component\Core\JWKSet;
use Jose\Easy\Build;
use PHPUnit\Framework\TestCase;

final class ClientAppTest extends TestCase
{
    private $container;

    protected function setUp(): void
    {
        $this->container = $container = Container::getInstance();

        $container->bind(Issuer::class, function () {
            return new Issuer('phpunit-pool-id', 'local');
        });

        $container->bind(RepositoryContract::class, function () {
            $repository = new Repository(new ArrayStore());

            $repository->set('jwks', file_get_contents(__DIR__ .'/fixtures/jwt.key.pub'));

            return $repository;
        });

        $container->bind(ClientAppRepository::class, FakeClientAppRepository::class);
    }

    private function clientAppToken(string $clientApp): string
    {
        $jwk = JWKSet::createFromJson(file_get_contents(__DIR__ . '/fixtures/jwt.key'));

        $time = time();

        return Build::jws()
            ->exp($time + 3600)
            ->iat($time)
            ->nbf($time)
            ->jti('token-id', true)
            ->alg('RS256')
            ->iss('https://cognito-idp.local.amazonaws.com/phpunit-pool-id')
            ->sub('testing')
            ->claim('client_app', $clientApp, true)
            ->sign($jwk->get(0));
    }

    public function test_client_app_will_be_converted_into_authenticatable_user()
    {
        $token = $this->clientAppToken('555');

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
}
