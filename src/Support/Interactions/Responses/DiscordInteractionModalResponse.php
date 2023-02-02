<?php

namespace Nwilging\LaravelDiscordBot\Support\Interactions\Responses;

use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableModalComponent;
use Nwilging\LaravelDiscordBot\Services\DiscordInteractionService;
use Nwilging\LaravelDiscordBot\Support\Components\GenericTextInputInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBot\Support\Traits\HasDiscordInteractions;

abstract class DiscordInteractionModalResponse extends DiscordInteractionResponse implements DiscordInteractableComponent
{
    use HasDiscordInteractions {
        validate as interactionValidate;
    }

    public ?string $parameter = null;
    protected string $title;
    /**
     * @var GenericTextInputInteractableComponent[]
     */
    protected array $components = [];

    public function __construct(string $title, array $components = [], ?string $parameter = null, ?int $status = 200)
    {
        $this->title = $title;
        $this->components = $components;
        $this->parameter = $parameter;
        parent::__construct(DiscordComponent::REPLY_WITH_MODAL, $status);
    }

    public function component(GenericTextInputInteractableComponent $component): static
    {
        $this->components[] = $component;
        return $this;
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return null;
    }

    public function populateFromInteractionRequest(array $interactionRequest): void {
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

    final public function onInteract(array $interactionRequest): void
    {
        $this->onModalSubmitted($interactionRequest);
    }

    public function getComponentWithParameter(string $parameter): ?GenericTextInputInteractableComponent
    {
        return array_filter($this->components, fn(DiscordComponent $component) => $component instanceof GenericTextInputInteractableComponent && $component->getParameter() == $parameter)[0] ?? null;
    }

    public function getSubmitedValueForComponentWithParameter(string $parameter): ?string
    {
        return $this->getComponentWithParameter($parameter)?->value;
    }

    abstract public function onModalSubmitted(array $interactionRequest): void;

    public function validate(): void
    {
        $this->interactionValidate();

        if (count($this->components) > 5) {
            throw new \Exception(sprintf("Discord does not allow more than 5 components in any Modal Responses. https://discord.com/developers/docs/interactions/receiving-and-responding#interaction-response-object-modal"));
        }
        if (count($this->components) < 1) {
            throw new \Exception(sprintf("Discord Modal Responses needs at least one component. https://discord.com/developers/docs/interactions/receiving-and-responding#interaction-response-object-modal"));
        }
        foreach ($this->components as $component) {
            if (!($component instanceof GenericTextInputInteractableComponent)) {
                throw new \Exception(sprintf("Support for components in modals is currently limited to type 4 (Text Input). https://discord.com/developers/docs/interactions/receiving-and-responding#interaction-response-object-modal"));
            }
            $component->validate();
        }
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getData(): ?array
    {
        return array_filter([
            'custom_id'  => $this->getCustomId(),
            'title'      => $this->title,
            'components' => [
                [
                    'type'       => DiscordComponent::TYPE_ACTION_ROW,
                    'components' => array_map(fn(GenericTextInputInteractableComponent $component) => $component->toArray(), $this->components)
                ]
            ],
        ]);
    }
}