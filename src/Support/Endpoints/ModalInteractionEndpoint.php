<?php

namespace Nwilging\LaravelDiscordBot\Support\Endpoints;

use Nwilging\LaravelDiscordBot\Services\DiscordInteractionService;

abstract class ModalInteractionEndpoint extends InteractionEndpoint
{
    /**
     * @var array<string, string>
     */
    protected array $inputs = [];

    final public function onInteract(array $interactionRequest): void
    {
        $this->getRelatedComponentEndpoint()->onResponseModalSubmitted($this, $interactionRequest);
    }

    public function getSubmittedValueForComponentWithId(mixed $id): ?string
    {
        return $this->inputs[$id] ?? null;
    }

    public function populateFromInteractionRequest(array $interactionRequest): void
    {
        foreach ($interactionRequest['data']['components'][0]['components'] as $component) {
            $this->inputs[$component['custom_id']] = $component['value'];
        }
    }

    protected function getRelatedComponentEndpoint(): InteractionEndpoint
    {
        /** @var DiscordInteractionService $discordInteractionService */
        $discordInteractionService = app()->make(DiscordInteractionService::class);
        return $discordInteractionService->getComponentFromCustomId($this->actionValue, $this->token);
    }
}