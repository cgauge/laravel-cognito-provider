<?php

declare(strict_types=1);

namespace CustomerGauge\Cognito;

use Jose\Component\Checker\ClaimCheckerManager;
use Jose\Component\Checker\ExpirationTimeChecker;
use Jose\Component\Checker\IssuerChecker;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSLoader;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;

use function json_decode;

final class TokenParser
{
    public function __construct(private KeyResolver $keyResolver)
    {
    }

    public function parse(string $token): mixed
    {
        $jws = $this->loadAndVerifyWithKeySet($token);

        $payload = json_decode($jws->getPayload(), true);

        $claimCheckerManager = new ClaimCheckerManager([
            new IssuerChecker([$this->keyResolver->issuer()->toString()]),
            new ExpirationTimeChecker(),
        ]);

        $claimCheckerManager->check($payload);

        return $payload;
    }

    private function loadAndVerifyWithKeySet(string $token): JWS
    {
        $jwsVerifier = new JWSVerifier(new AlgorithmManager([new RS256()]));

        $serializerManager = new JWSSerializerManager([new CompactSerializer()]);

        $jwsLoader = new JWSLoader($serializerManager, $jwsVerifier, null);

        $jwkset = JWKSet::createFromJson($this->keyResolver->jwkset());

        return $jwsLoader->loadAndVerifyWithKeySet($token, $jwkset, $signature);
    }
}
