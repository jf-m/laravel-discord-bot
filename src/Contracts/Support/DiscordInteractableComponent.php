<?php

namespace Nwilging\LaravelDiscordBot\Contracts\Support;

use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;
use Nwilging\LaravelDiscordBot\Support\Endpoints\InteractionEndpoint;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBot\Support\Interactions\Responses\GenericDiscordInteractionModalResponse;

interface DiscordInteractableComponent extends DiscordComponent
{
    public function validate(): void;

    public function getInteractionEndpoint(): ?InteractionEndpoint;

    public function getCustomId(): ?string;

}