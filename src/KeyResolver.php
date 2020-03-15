<?php declare(strict_types=1);

namespace CustomerGauge\Cognito;

use Illuminate\Contracts\Cache\Repository;

final class KeyResolver
{
    private $cache;

    private $issuer;

    public function __construct(Issuer $issuer, Repository $cache)
    {
        $this->issuer = $issuer;
        $this->cache = $cache;
    }

    public function jwkset(): string
    {
        $url = $this->issuer->toString() . '/.well-known/jwks.json';

        return $this->cache->remember('jwks', 7200, function () use ($url) {
            return file_get_contents($url);
        });
    }

    public function issuer(): Issuer
    {
        return $this->issuer;
    }
}
