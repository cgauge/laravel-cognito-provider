<?php declare(strict_types=1);

namespace Tests\CustomerGauge\Cognito;

use CustomerGauge\Cognito\Contracts\UserFactory;
use CustomerGauge\Cognito\Issuer;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as RepositoryContract;
use Jose\Component\Core\JWKSet;
use Jose\Easy\Build;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\CustomerGauge\Cognito\Fixtures\MyUserFactory;

abstract class TestCase extends BaseTestCase
{
    protected $container;

    protected function setUp(): void
    {
        $this->container = $container = Container::getInstance();

        $container->bind(Issuer::class, function () {
            return new Issuer('phpunit-pool-id', 'local');
        });

        $container->bind(RepositoryContract::class, function () {
            $repository = new Repository(new ArrayStore());

            $repository->set('jwks', file_get_contents(__DIR__ .'/Fixtures/jwt.key.pub'));

            return $repository;
        });

        $container->bind(UserFactory::class, MyUserFactory::class);
    }

    protected function jwtToken(array $claims): string
    {
        $jwk = JWKSet::createFromJson(file_get_contents(__DIR__ . '/Fixtures/jwt.key'));

        $time = time();

        $builder = Build::jws()
            ->exp($time + 3600)
            ->iat($time)
            ->nbf($time)
            ->jti('token-id', true)
            ->alg('RS256')
            ->iss('https://cognito-idp.local.amazonaws.com/phpunit-pool-id')
            ->sub('testing');

        foreach ($claims as $key => $value) {
            $builder->claim($key, $value, true);
        }

        return $builder->sign($jwk->get(0));
    }
}
