<?php declare(strict_types=1);

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

final class TokenParser
{
    private $keyResolver;

    public function __construct(KeyResolver $keyResolver)
    {
        $this->keyResolver = $keyResolver;
    }
    
    /**
     * This public method is useful for parsing the token from PHPUnit. It is not intended for production use.
     */
    public function unverifiedPayload(string $token): array
    {
        $jws = $this->loadAndVerifyWithKeySet($token);

        return json_decode($jws->getPayload(), true);
    }

    public function parse(string $token)
    {
        $payload = $this->unverifiedPayload($token);

        $claimCheckerManager = new ClaimCheckerManager([
            new IssuerChecker([$this->keyResolver->issuer()->toString()]),
            new ExpirationTimeChecker,
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
