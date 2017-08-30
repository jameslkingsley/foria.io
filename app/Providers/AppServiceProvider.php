<?php

namespace App\Providers;

use Stripe\Stripe;
use OpenTok\OpenTok;
use Laravel\Cashier\Cashier;
use Aws\Credentials\Credentials;
use Aws\CloudFront\CloudFrontClient;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Aws\ElasticTranscoder\ElasticTranscoderClient;

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
        $awsCredentials = new Credentials(
            config('services.cloudfront.key'),
            config('services.cloudfront.secret')
        );

        $this->app->bind('Aws\CloudFront\CloudFrontClient', function ($app) use ($awsCredentials) {
            return CloudFrontClient::factory([
                'credentials' => $awsCredentials,
                'region' => config('services.cloudfront.region'),
                'version' => '2017-03-25',
            ]);
        });

        $this->app->bind('Aws\ElasticTranscoder\ElasticTranscoderClient', function ($app) use ($awsCredentials) {
            return ElasticTranscoderClient::factory([
                'credentials' => $awsCredentials,
                'region' => config('services.cloudfront.region'),
                'version' => 'latest',
            ]);
        });

        require_once app_path('Support/Helpers.php');
    }
}
