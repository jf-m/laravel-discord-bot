<?php

namespace Nwilging\LaravelDiscordBot\Facades;

use Illuminate\Support\Facades\Facade;

class Discord extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'laravel-discord-bot'; }
}