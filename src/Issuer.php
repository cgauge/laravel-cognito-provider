<?php declare(strict_types=1);

namespace CustomerGauge\Cognito;

final class Issuer
{
    private $userPoolId;

    private $region;

    public function __construct(string $userPoolId, string $region)
    {
        $this->userPoolId = $userPoolId;
        $this->region = $region;
    }

    public function toString(): string
    {
        $url = 'https://cognito-idp.%s.amazonaws.com/%s';

        return sprintf($url, $this->region, $this->userPoolId);
    }
}
