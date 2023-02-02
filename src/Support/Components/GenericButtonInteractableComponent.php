<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;


use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Traits\HasDiscordInteractions;
use Nwilging\LaravelDiscordBot\Support\Traits\FiltersRecursive;
use Nwilging\LaravelDiscordBot\Support\Traits\HasEmojiObject;

abstract class GenericButtonInteractableComponent implements DiscordComponent, DiscordInteractableComponent
{
    use FiltersRecursive, HasEmojiObject, HasDiscordInteractions;

    public const STYLE_PRIMARY = 1;
    public const STYLE_SECONDARY = 2;
    public const STYLE_SUCCESS = 3;
    public const STYLE_DANGER = 4;
    public const STYLE_LINK = 5;

    protected int $style;

    protected string $label;

    protected ?bool $disabled = null;

    public function __construct(int $style, string $label, ?string $parameter = null)
    {
        $this->parameter = $parameter;
        $this->style = $style;
        $this->label = $label;
    }

    public function getType(): int
    {
        return DiscordComponent::TYPE_BUTTON;
    }

    /**
     * Whether the button is disabled
     *
     * @see https://discord.com/developers/docs/interactions/message-components#button-object-button-structure
     * @param bool $disabled
     * @return $this
     */
    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function toArray(): array
    {
        return $this->arrayFilterRecursive($this->mergeEmojiObject([
            'custom_id' => $this->getCustomId(),
            'type' => $this->getType(),
            'style' => $this->style,
            'label' => $this->label,
            'disabled' => $this->disabled,
        ]));
    }

    public function populateFromInteractionRequest(array $interactionRequest): void
    {

    }

    final public function onInteract(array $interactionRequest): void
    {
        $this->onClicked($interactionRequest);
    }

    abstract public function onClicked(array $interactionRequest): void;
}
