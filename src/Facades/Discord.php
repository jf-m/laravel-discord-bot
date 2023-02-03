<?php

namespace Nwilging\LaravelDiscordBot\Facades;

use Nwilging\LaravelDiscordBot\Contracts\Services\DiscordApiServiceContract;

class Discord
{
    protected static function getFacadeAccessor(): string
    {
        return DiscordApiServiceContract::class;
    }
}