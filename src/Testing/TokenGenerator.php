<?php

declare(strict_types=1);

namespace CustomerGauge\Cognito\Testing;

use InvalidArgumentException;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Core\Util\JsonConverter;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;

use function file_get_contents;
use function time;

final class TokenGenerator
{
    public string $jti = 'token-id';

    public string $algorithm = 'RS256';

    public string $issuer = 'https://cognito-idp.local.amazonaws.com/phpunit-pool-id';

    public string $subject = 'testing';

    public function __construct(private JWKSet $jwk)
    {
    }

    public static function fromFile(string $path): self
    {
        $key = file_get_contents($path);

        if ($key === false) {
            throw new InvalidArgumentException('Invalid file');
        }

        return new self(JWKSet::createFromJson($key));
    }

    /** @param mixed[] $attributes */
    public function sign(array $attributes): string
    {
        $time = time();

        $algorithmManager = new AlgorithmManager([new RS256()]);
        $jwsBuilder       = new JWSBuilder($algorithmManager);
        $payload          = JsonConverter::encode([
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
