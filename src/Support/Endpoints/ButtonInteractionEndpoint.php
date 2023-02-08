<?php

namespace Nwilging\LaravelDiscordBot\Support\Endpoints;

abstract class ButtonInteractionEndpoint extends InteractionEndpoint
{
    final public function onInteract(array $interactionRequest): void
    {
        $this->onClick($interactionRequest);
    }

    abstract public function onClick(array $interactionRequest): void;
}