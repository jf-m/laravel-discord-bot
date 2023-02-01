<?php

namespace Nwilging\LaravelDiscordBot\Contracts\Support;

use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;

interface DiscordInteractableModalComponent extends DiscordInteractableComponent
{
    public function setValue(string $value): void;
}