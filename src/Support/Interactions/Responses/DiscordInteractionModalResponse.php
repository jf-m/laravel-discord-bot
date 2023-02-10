<?php

namespace Nwilging\LaravelDiscordBot\Support\Interactions\Responses;

use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableModalComponent;
use Nwilging\LaravelDiscordBot\Services\DiscordInteractionService;
use Nwilging\LaravelDiscordBot\Support\Components\GenericTextInputInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Endpoints\InteractionEndpoint;
use Nwilging\LaravelDiscordBot\Support\Endpoints\ModalInteractionEndpoint;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBot\Support\Traits\HasDiscordInteractions;

class DiscordInteractionModalResponse extends DiscordInteractionResponse
{

    protected string $title;
    protected mixed $customId;
    /**
     * @var GenericTextInputInteractableComponent[]
     */
    protected array $components = [];

    public function __construct(string $title)
    {
        $this->title = $title;
        parent::__construct(DiscordComponent::REPLY_WITH_MODAL, 200);
    }

    public function withComponent(mixed $id, GenericTextInputInteractableComponent $component): static
    {
        $component->customId = $id;
        $this->components[] = $component;
        return $this;
    }

    public function prepareCustomId(InteractionEndpoint $endpoint): void
    {
        $this->customId = $endpoint->getCustomId();
    }

    public function validate(): void
    {
        if (count($this->components) > 5) {
            throw new \Exception(sprintf("Discord does not allow more than 5 components in any Modals Responses. https://discord.com/developers/docs/interactions/receiving-and-responding#interaction-response-object-modal"));
        }
        if (count($this->components) < 1) {
            throw new \Exception(sprintf("Discord Modals Responses needs at least one component. https://discord.com/developers/docs/interactions/receiving-and-responding#interaction-response-object-modal"));
        }
        foreach ($this->components as $component) {
            if (!($component instanceof GenericTextInputInteractableComponent)) {
                throw new \Exception(sprintf("Support for components in modals is currently limited to type 4 (Text Input). https://discord.com/developers/docs/interactions/receiving-and-responding#interaction-response-object-modal"));
            }
        }
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getData(): ?array
    {
        return array_filter([
            'custom_id'  => $this->customId,
            'title'      => $this->title,
            'components' => array_map(fn(GenericTextInputInteractableComponent $component) => [
                'type'       => DiscordComponent::TYPE_ACTION_ROW,
                'components' => [$component->toArray()]
            ], $this->components),
        ]);
    }
}