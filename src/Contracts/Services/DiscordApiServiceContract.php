<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Contracts\Services;

use Nwilging\LaravelDiscordBot\Support\Embed;

interface DiscordApiServiceContract
{
    public function sendMessage(string $channelId, ?string $message = null, ?array $embeds = null, ?array $components = null, ?array $options = null): array;
}
