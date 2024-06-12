<?php

declare(strict_types=1);

namespace CustomerGauge\Cognito;

use Illuminate\Contracts\Cache\Repository;
use InvalidArgumentException;

use function file_get_contents;

final class KeyResolver
{
    public function __construct(private Issuer $issuer, private Repository $cache)
    {
    }

    public function jwkset(): string
    {
        $url = $this->issuer->toString() . '/.well-known/jwks.json';

        return $this->cache->remember('jwks', 7200, static function () use ($url) {
            $content = file_get_contents($url);

            if ($content === false) {
                throw new InvalidArgumentException('Invalid JWKS file');
            }

            return $content;
        });
    }

    public function issuer(): Issuer
    {
        return $this->issuer;
    }
}
