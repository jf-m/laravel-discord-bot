<?php

namespace Nwilging\LaravelDiscordBot\Support\Endpoints;

abstract class SelectMenuInteractionEndpoint extends InteractionEndpoint
{
    final public function onInteract(array $interactionRequest): void
    {
        $this->onMenuItemsSubmit($interactionRequest['data']['values'] ?? [], $interactionRequest);
    }

    abstract public function onMenuItemsSubmit(array $interactionRequest): void;

    public function populateFromInteractionRequest(array $interactionRequest): void {
        $values = $interactionRequest['data']['values'] ?? [];
        $submittedComponents = [];
        foreach ($values as $value) {
            $submittedComponents[] = new SelectOptionObject($value, $value);
        }
        $this->options = $submittedComponents;
    }

}