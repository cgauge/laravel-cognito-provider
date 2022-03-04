<?php declare(strict_types=1);

namespace CustomerGauge\Cognito\Testing;

use Jose\Component\Core\JWKSet;
use Jose\Easy\Build;

final class TokenGenerator
{
    private $jwk;

    public $jti = 'token-id';

    public $algorithm = 'RS256';

    public $issuer = 'https://cognito-idp.local.amazonaws.com/phpunit-pool-id';

    public $subject = 'testing';

    public function __construct(JWKSet $jwk)
    {
        $this->jwk = $jwk;
    }

    public static function fromFile(string $path): self
    {
        $key = file_get_contents($path);

        return new self(JWKSet::createFromJson($key));
    }

    public function sign(array $attributes): string
    {
        $time = time();

        $builder = Build::jws()
            ->exp($time + 3600)
            ->iat($time)
            ->nbf($time)
            ->jti($this->jti, true)
            ->alg($this->algorithm)
            ->iss($this->issuer)
            ->sub($this->subject);

        foreach ($attributes as $key => $value) {
            $builder->claim($key, $value, true);
        }

        return $builder->sign($this->jwk->get(0));
    }
}