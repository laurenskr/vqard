<?php

namespace Qabana\StatamicVcard;

use Illuminate\Support\Facades\Event;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Events\EntrySaved;
use Qabana\StatamicVcard\Listeners\GenerateVcardQrCode;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon(): void
    {
        // Config
        $this->mergeConfigFrom(__DIR__.'/../config/vcard.php', 'vcard');

        $this->publishes([
            __DIR__.'/../config/vcard.php' => config_path('vcard.php'),
        ], 'vcard-config');

        // Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'vcard');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/vcard'),
        ], 'vcard-views');

        // Blueprint
        $this->publishes([
            __DIR__.'/../resources/blueprints' => resource_path('blueprints/collections/vcards'),
        ], 'vcard-blueprint');

        // Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Register the listener directly — no EventServiceProvider needed in the host app
        Event::listen(EntrySaved::class, GenerateVcardQrCode::class);
    }
}