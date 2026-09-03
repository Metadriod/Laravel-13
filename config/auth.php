<?php

use App\Models\ApiKey;
use App\Models\User;
use App\Services\Verification\Methods\EmailVerificationChannel;
use App\Services\Verification\Methods\GAuthenticatorVerificationApp;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'token'),
        /* 'guard' => env('AUTH_GUARD', 'web'), original config */
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'token' => [
            'driver' => 'multi_token_driver',
            'provider' => 'users', // Even tho we don't technically use a provider, Spatie needs for roles and permissions
        ],
        'api_key' => [
            'driver' => 'api_key_driver',
            'provider' => 'api_keys',
        ],
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],
        'api_keys' => [
            'driver' => 'eloquent',
            'model' => ApiKey::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

    /*
     |--------------------------------------------------------------------------
     | Email verification and Account verification expiration
     |--------------------------------------------------------------------------
     |
     | Defined here are the expiration (in minutes) of the email and
     | account verification notifications
     |
     */

    'verification' => [
        'expiration' => [
            'email' => env('VERIFY_EMAIL_EXPIRATION_MINUTES', 60),
            'account' => env('VERIFY_ACCOUNT_EXPIRATION_MINUTES', 10080),
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Multi Token Auth Mechanism Switch
     |--------------------------------------------------------------------------
     |
     | Set if Sanctum or JWT authentication are enabled.
     |
    */
    'mechanism' => [
        'sanctum_enabled' => env('SANCTUM_AUTH_ENABLED', true),
        'jwt_enabled' => env('JWT_AUTH_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Factor Authentication Methods
    |--------------------------------------------------------------------------
    |
    | This configuration defines the available support MFA Verification Methods.
    | Each method must be a class that extends the App\Services\Verification\AppVerificationMethod
    | and App\Services\Verification\DeliveryVerificationMethod abstract classes
    |
    */
    'mfa_methods' => [
        EmailVerificationChannel::class,
        GAuthenticatorVerificationApp::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Factors Code Expirations
    |--------------------------------------------------------------------------
    |
    | This configuration defines the expiration time (in seconds) of verification
    | codes implemented by delivery-based method classes
    |
    */
    'verification_codes' => [
        'expiration' => [
            'email' => 12 * 60, // 12 minutes
        ],
    ],

];
