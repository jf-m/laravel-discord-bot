<?php

namespace Nwilging\LaravelDiscordBot\Contracts\Support;

use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBot\Support\Interactions\Responses\GenericDiscordInteractionModalResponse;

interface DiscordInteractableComponent extends DiscordComponent
{
    public function populateFromInteractionRequest(array $interactionRequest): void;

    public function onInteract(array $interactionRequest): void;

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse;

    public function createResponseModal(string $title, array $inputComponents): DiscordInteractionResponse;

    public function onQueue(string $queue);

    public function onConnection(string $connection);

    public function shouldDispatchSync();

    public function getParameter(): mixed;

    public function getToken(): ?string;

    public function sendFollowupMessage(DiscordMessage $discordMessage): array;

    public function deleteInitialInteractionResponse(): array;

    public function editInitialInteractionResponse(DiscordMessage $discordMessage): array;

    public function onResponseModalSubmitted(GenericDiscordInteractionModalResponse $modal, array $interactionRequest): void;

    public function getInteractionResponseForResponseModal(GenericDiscordInteractionModalResponse $modal, array $interactionRequest): ?DiscordInteractionResponse;
}