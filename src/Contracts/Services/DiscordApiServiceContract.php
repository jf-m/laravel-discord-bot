<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Contracts\Services;

use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;

interface DiscordApiServiceContract
{
    public function sendMessage(DiscordMessage $discordMessage): array;
}
