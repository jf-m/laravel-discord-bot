<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Contracts\Services;

use Illuminate\Http\Request;
use Nwilging\LaravelDiscordBot\Support\InteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;

interface DiscordInteractionServiceContract
{
    public function getComponentFromCustomId(string $customId): InteractableComponent;

    public function handleInteractionRequest(Request $request): DiscordInteractionResponse;
}
