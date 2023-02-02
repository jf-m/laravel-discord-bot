<?php

namespace Nwilging\LaravelDiscordBot\Support\Interactions\Responses;

use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableModalComponent;
use Nwilging\LaravelDiscordBot\Services\DiscordInteractionService;
use Nwilging\LaravelDiscordBot\Support\Components\GenericTextInputInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBot\Support\Traits\HasDiscordInteractions;

final class GenericDiscordInteractionModalResponse extends DiscordInteractionModalResponse
{
    public function populateFromInteractionRequest(array $interactionRequest): void
    {
        $components = $interactionRequest['data']['components'][0]['components'];
        /** @var DiscordInteractionService $discordInteractionService */
        $discordInteractionService = app()->make(DiscordInteractionService::class);
        foreach ($components as $component) {
            /** @var DiscordInteractableModalComponent $component */
            $componentObj = $discordInteractionService->getComponentFromCustomId($component['custom_id'], '');
            $componentObj->setValue($component['value']);
            $this->components[] = $componentObj;
        }
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        $this->getRelatedComponent()->getInteractionResponseForResponseModal($this);
    }

    public function onModalSubmitted(array $interactionRequest): void
    {
        $this->getRelatedComponent()->onResponseModalSubmitted($this, $interactionRequest);
    }

    protected function getRelatedComponent(): DiscordInteractableComponent
    {
        /** @var DiscordInteractionService $discordInteractionService */
        $discordInteractionService = app()->make(DiscordInteractionService::class);
        $component = $discordInteractionService->getComponentFromCustomId($this->parameter);
        if ($component instanceof DiscordInteractableComponent) {
            return $component;
        } else {
            throw new \Exception(get_class($component) . ' is expected to inherit a DiscordInteractableComponent in order to use GenericDiscordInteractionModalResponse');
        }

    }
}