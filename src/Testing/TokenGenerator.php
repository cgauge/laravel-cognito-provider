<?php declare(strict_types=1);

namespace CustomerGauge\Cognito\Testing;

use Jose\Component\Core\JWKSet;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;

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

        $algorithmManager = new AlgorithmManager([new RS256()]);
        $jwsBuilder = new JWSBuilder($algorithmManager);
        $payload = json_encode([
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + 3600,
            'iss' => $this->issuer,
            'jti' => $this->jti,
            'sub' => $this->subject,
        ] + $attributes);

        $jws = $jwsBuilder->create()
            ->withPayload($payload)
            ->addSignature($this->jwk->get(0), ['alg' => $this->algorithm])
            ->build();

        return (new CompactSerializer())->serialize($jws);
    }
}