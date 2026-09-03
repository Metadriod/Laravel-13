<?php

namespace App\Providers;

use App\Enums\DbPolyType;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class PolymorphMapProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            DbPolyType::USER->value => User::class,
            DbPolyType::API_KEY->value => ApiKey::class,
        ]);
    }
}
