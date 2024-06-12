<?php

declare(strict_types=1);

namespace CustomerGauge\Cognito;

use function sprintf;

final class Issuer
{
    public function __construct(private string $userPoolId, private string $region)
    {
    }

    public function toString(): string
    {
        $url = 'https://cognito-idp.%s.amazonaws.com/%s';

        return sprintf($url, $this->region, $this->userPoolId);
    }
}
