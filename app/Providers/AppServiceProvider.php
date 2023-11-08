<?php

namespace App\Providers;

use App\Models\RecipeDetail;
use Illuminate\Support\ServiceProvider;
use App\Models\Spoonacular;
use App\Wrappers\SpoonacularWrapper;
use Cristal\ApiWrapper\Transports\Transport;
use Curl\Curl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SpoonacularWrapper::class, function(){
            $transport = new Transport(
                'https://api.spoonacular.com', 
                $this->app->make(Curl::class)
            );

            return new SpoonacularWrapper($transport);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Spoonacular::setApi($this->app->make(SpoonacularWrapper::class));
        RecipeDetail::setApi($this->app->make(SpoonacularWrapper::class));
    }
}
