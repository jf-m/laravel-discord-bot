<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;


use Nwilging\LaravelDiscordBot\Support\Component;
use Nwilging\LaravelDiscordBot\Support\InteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Traits\FiltersRecursive;
use Nwilging\LaravelDiscordBot\Support\Traits\HasEmojiObject;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class GenericButtonInteractableComponent extends InteractableComponent
{
    use FiltersRecursive, HasEmojiObject;

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
        parent::__construct($parameter);

        $this->style = $style;
        $this->label = $label;
    }

    protected function getType(): int
    {
        return Component::TYPE_BUTTON;
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

    final public function onInteract(array $interactionRequest): void
    {
        $this->onClicked($interactionRequest);
    }

    abstract public function onClicked(array $interactionRequest): void;
}
