<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];
    
    public function boot(): void
    {
        $this->registerPolicies();

        // Here you can configure Spatie permissions if needed

    }
}