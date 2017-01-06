<?php

namespace Mailchimp;

use Illuminate\Support\ServiceProvider;

class MailchimpServiceProvider extends ServiceProvider
{

    /**
     * Register paths to be published by the publish command.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/mailchimp.php' => config_path('mailchimp.php')
        ]);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Mailchimp\Mailchimp', function ($app, $args) {

            $config = $app['config']['mailchimp'];

            $apikey = isset($args[0]) ? $args[0] : $config['apikey'];
            $clientOptions = isset($args[1]) ? $args[1] : [];

            return new Mailchimp($apikey, $clientOptions);
        });
    }
}
