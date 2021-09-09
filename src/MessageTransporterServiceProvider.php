<?php

namespace Zipzoft\MessageTransporter;

use Illuminate\Support\ServiceProvider;
use Zipzoft\MessageTransporter\Broadcasters\ServiceBroadcaster;

class MessageTransporterServiceProvider extends ServiceProvider
{


    public function register()
    {
        $this->setUpConfig();

        $this->app->singleton(Factory::class, function ($app) {
            return new Manager($app);
        });

        $this->app->singleton(ServiceBroadcaster::class, function ($app) {
            return $app->make(Factory::class)->connection();
        });

        $this->app['events']->listen('*', BroadcastAppServicesListener::class);
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('message-transporter.php'),
            ], 'config');
        }
    }


    private function setUpConfig()
    {
        $this->mergeConfigFrom(__DIR__."/../config/config.php", "message-transporter");

        $prefix = config('message-transporter.connection_prefix');

        foreach (['producer', 'consumer'] as $name) {
            $this->mergeConfigFrom(__DIR__."/../config/redis.{$name}.php", "database.redis.{$prefix}{$name}");
        }
    }

}
