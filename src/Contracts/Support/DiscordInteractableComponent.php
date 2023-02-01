<?php

namespace Nwilging\LaravelDiscordBot\Contracts\Support;

use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;

interface DiscordInteractableComponent extends DiscordComponent
{
    public function onInteract(array $interactionRequest): void;

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse;

    public function onQueue(string $queue);

    public function onConnection(string $connection);

    public function shouldDispatchSync();

    public function getParameter(): ?string;
}