<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Contracts\Services;

use Illuminate\Http\Request;
use Nwilging\LaravelDiscordBot\Support\Endpoints\InteractionEndpoint;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;

interface DiscordInteractionServiceContract
{
    public function getComponentFromCustomId(string $customId, string $token): InteractionEndpoint;

    public function handleInteractionRequest(Request $request): DiscordInteractionResponse;
}
