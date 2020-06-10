![Build](https://github.com/cgauge/laravel-cognito-provider/workflows/Tests/badge.svg)
[![Code Coverage](https://scrutinizer-ci.com/g/cgauge/laravel-cognito-provider/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/cgauge/laravel-cognito-provider/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cgauge/laravel-cognito-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cgauge/laravel-cognito-provider/?branch=master)

# Laravel Cognito Provider 🔑

This library provides a CognitoUserProvider for Laravel.

# Installation

```bash
composer require customergauge/laravel-cognito-provider
```

# Usage

### Auth configuration

In the `auth.php` file, add the following settings:

Default Guard

```php
    'defaults' => [
        'guard' => 'cognito-token',
        'passwords' => 'users',
    ],
```

The new Guard configuration
```php
    'guards' => [
        'cognito-token' => [
            'driver' => 'token',
            'provider' => 'cognito-provider',
            'storage_key' => 'cognito_token',
            'hash' => false,
        ],
    ],
```

The User Provider configuration
```php

    'providers' => [
        'cognito-provider' => [
            'driver' => \CustomerGauge\Cognito\CognitoUserProvider::class,
        ],
    ],
```

Cognito Environment Variables
```php
    /*
    |--------------------------------------------------------------------------
    | Cognito Custom Configuration
    |--------------------------------------------------------------------------
    |
    | The following configuration is not part of standard Laravel application.
    | We use it to configure the CognitoUserProvider process so that we can
    | properly validate the JWT token provided by AWS Cognito.
    |
    */

    'cognito' => [
        'pool' => env('AWS_COGNITO_USER_POOL_ID'),
        'region' => env('AWS_COGNITO_USER_POOL_REGION'),
    ],
```

### Auth Middleware

Configure the `auth` middleware at `App\Http\Kernel` with `'auth:cognito-token'`

