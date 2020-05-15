<?php

namespace Composer\Push\Providers;

use Illuminate\Support\ServiceProvider;
use JPush\Client as JPush;

class JPushServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton(JPush::class, function () {
            $client = new JPush(config('jpush.app_key'), config('jpush.master_secret'), config('jpush.file'));
            return $client;
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [JPush::class];
    }
}
