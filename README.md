<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About the Startker Kit

This starter kit API is a Laravel 13 RESTFul starter kit for SPA and mobile clients. This kit includes the following features:
- Implementation of a Token-based Authentication with [Sanctum](https://laravel.com/docs/9.x/sanctum) and [JWT](https://github.com/stechstudio/laravel-jwt)
- Implementation of Role-based Access Control with [Spatie](https://spatie.be/docs/laravel-permission/v5/introduction)
- Implementation of CRUD for user profile with profile picture upload
- Implementation Forgot and Reset Password with Email Notification
- International phone number validation support
- System Alert notifications for critical errors/warnings (Email)
- Pipeline implementation of HTTP query filters
- Implementation of search functionality with DB fulltext indexes
- Sample webhooks available with API Key authentication and permission-based authorization
- Modular implementation of Multi-factor Authentication with Email OTP and Google Authenticator
- [Clockwork](https://github.com/itsgoingd/clockwork) installed for performance monitoring while in development. Remember to install the browser extension
- Gitlab MR template in `.gitlab/merge_request_templates`
- Feature and Unit tests coverage

## Set up your local development environment
- Minimum of PHP 8.3 installed with a database engine that supports JSON types and possibly with full text search (e.g. MySQL8, MariaDB 10.5)
- Create a **.env** and a **.env.testing** files from the **.env.example.redis**, use the **.env.example** if you will not use Redis that came with this project. For security purposes, please request the contents of these files from the SCRUM master / Tech Lead
  - Multi-Auth Update: Make sure that `SANCTUM_AUTH_ENABLED`, `JWT_AUTH_ENABLED`, `WEBHOOKS_ENABLED` are set to true in `.env.testing`
  - Make sure that the `APP_KEY` values from the `.env` are the same in `.env.testing`
  - Create a new database for testing based on your `.env` file e.g. `DB_DATABASE=db_database`, your testing database inside `.env.testing` should be `DB_DATABASE=db_database_test`
- Locate your **php.ini** file and change the value **upload_max_filesize** to **10M**. See this [guide](https://devanswers.co/ubuntu-php-php-ini-configuration-file/) if you're having trouble finding the directory of your php.ini file
- Make sure you have MySQL and Redis running locally (or depending on what's stated in the **.env** file)
- If you don't want to run Redis, you need to run `php artisan session:table` to create a Session table within your database. You should run this commend prior to App initialization (`php artisan app:init`)
- If you are using Laravel Cloud, the **.env** configuration will be `CACHE_STORE=database` and `SCHEDULE_CACHE_DRIVER=database`. You should run `php artisan cache:table` prior to App initialization (`php artisan app:init`)
- Run the command `composer install`  to install all the project and dev dependencies
- Run the command `php artisan app:init` to initialize the project. The command will run:
  - App key generation
  - DB migrations
  - DB Seeders
- To check if everything is working as expected, run: `php artisan test`

## Tools ready for you
Runs a [code styler](https://laravel.com/docs/9.x/pint) for consistency and generate [IDE helper PHP Docs](https://github.com/barryvdh/laravel-ide-helper). See the command at `app/Console/Commands/StyleFixer.php`
```
php artisan app:styler -i
```
\
Create a user with role. See the command at `app/Console/Commands/CreateUser.php`
```
php artisan user:create
```
\
Create an API Key for Webhook integration. See the command at `app/Console/Commands/CreateApiKey.php`
```
php artisan api_key:create
```
\
Setup MFA configurations. See command at `app/Console/Commands/SetupMfa.php`
```
php artisan mfa:setup
```
\
Delete expired MFA attempt records in the database (is also scheduled to run every 12AM). See command at `app/Console/Commands/PurgeExpiredMfaAttempts.php`
```
php artisan mfa:prune-expired-attempts
```
\
Clear Laravel 12 Logs: This is being used for dev environment only. `app/Console/Commands/ClearLogs.php`
```
php artisan clear:logs
```

## Serve the API locally
- Terminal 1: Run `php artisan serve`
- Terminal 2: Run `php artisan queue:work --queue=otp,emails,dev_alerts,default`

## Style Guide Ver. 0.2
- Use **FormRequest** validators when available
- Favor single quotes over double quotes
- Make use of type-hinting
- Extend the **ApiController** for all your API controllers
- Use `snake_case` for DB table columns, request inputs, and resource views
- Use `PascalCase` for class names
- User `camelCase` for variable, method, and function names
- Create separate API route files per resource/feature. Load all of them in `routes/api.php`
- Follow and implement the [PHPDoc](https://docs.phpdoc.org/3.0/guide/guides/docblocks.html) style guide
- Use `app\Exceptions\Handler.php` for centralized error handling
- Stick with Eloquent as much as possible, create services to abstract or remove duplicating code
- We use the `tests/Feature` specifically for API endpoint tests, and `tests/Unit` for unit and integration tests

## Note
- The default timezone set for dates and timestamps is `Asia/Manila`. You can change this in `config/app.php`
- Extend the Database\Seeders\CiCdCompliantSeeder class when making a seeder that will be called in database/seeders/DatabaseSeeder.php

## Author
- Jego Carlo Ramos
- Josef Friedrich Baldo (port for Laravel 13)

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).