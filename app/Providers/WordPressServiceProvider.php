<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WordPressService;

class WordPressServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WordPressService::class, function ($app) {
            return new WordPressService();
        });
    }
    
    public function boot(): void
    {
        //
    }
}