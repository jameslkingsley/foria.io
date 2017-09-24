<?php

namespace App\Providers;

use Stripe\Stripe;
use OpenTok\OpenTok;
use App\Models\Report;
use App\Support\Reference;
use Laravel\Cashier\Cashier;
use Aws\Credentials\Credentials;
use Aws\CloudFront\CloudFrontClient;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        View::share('opentokApiKey', config('opentok.api_key'));
        View::share('reportableReasons', Report::reasons());

        Stripe::setApiKey(config('services.stripe.secret'));

        Route::bind('ref', function ($hash) {
            return reference($hash);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $awsCredentials = new Credentials(
            config('services.aws.cloudfront.key'),
            config('services.aws.cloudfront.secret')
        );

        $this->app->bind('Aws\CloudFront\CloudFrontClient', function ($app) use ($awsCredentials) {
            return CloudFrontClient::factory([
                'credentials' => $awsCredentials,
                'region' => config('services.aws.cloudfront.region'),
                'version' => '2017-03-25',
            ]);
        });

        $this->app->bind('Aws\ElasticTranscoder\ElasticTranscoderClient', function ($app) use ($awsCredentials) {
            return ElasticTranscoderClient::factory([
                'credentials' => $awsCredentials,
                'region' => config('services.aws.ets.region'),
                'version' => 'latest',
            ]);
        });

        require_once app_path('Support/Helpers.php');
    }
}
