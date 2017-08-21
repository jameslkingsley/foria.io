<?php

namespace App\Providers;

use Stripe\Stripe;
use OpenTok\OpenTok;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::share('opentokApiKey', config('opentok.api_key'));

        Stripe::setApiKey(config('services.stripe.secret'));

        // Cashier::useCurrency('gbp', '£');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path('Support/Helpers.php');
    }
}
