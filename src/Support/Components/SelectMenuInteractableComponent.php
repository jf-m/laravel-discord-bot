<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;


use Nwilging\LaravelDiscordBot\Support\Component;
use Nwilging\LaravelDiscordBot\Support\InteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Objects\SelectOptionObject;
use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Select Menu InteractableComponent
 * @see https://discord.com/developers/docs/interactions/message-components#select-menu-object-select-menu-structure
 */
abstract class SelectMenuInteractableComponent extends InteractableComponent
{
    use MergesArrays;

    /**
     * @var SelectOptionObject[]
     */
    protected array $options;

    protected ?string $placeholder = null;

    protected ?int $minValues = null;

    protected ?int $maxValues = null;

    protected ?bool $disabled = null;

    /**
     * @param SelectOptionObject[] $options
     * @param string|null $parameter
     */
    public function __construct(array $options, ?string $parameter = null)
    {
        parent::__construct($parameter);
        $this->options = $options;
    }

    /**
     * Custom placeholder text if nothing is selected, max 150 characters
     *
     * @see https://discord.com/developers/docs/interactions/message-components#select-menu-object-select-menu-structure
     * @param string $placeholder
     * @return $this
     */
    public function withPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * The minimum number of items that must be chosen; default 1, min 0, max 25
     *
     * @see https://discord.com/developers/docs/interactions/message-components#select-menu-object-select-menu-structure
     * @param int $minValues
     * @return $this
     */
    public function withMinValues(int $minValues): self
    {
        $this->minValues = $minValues;
        return $this;
    }

    /**
     * The maximum number of items that can be chosen; default 1, max 25
     *
     * @see https://discord.com/developers/docs/interactions/message-components#select-menu-object-select-menu-structure
     * @param int $maxValues
     * @return $this
     */
    public function withMaxValues(int $maxValues): self
    {
        $this->maxValues = $maxValues;
        return $this;
    }

    /**
     * Disables the select
     *
     * @see https://discord.com/developers/docs/interactions/message-components#select-menu-object-select-menu-structure
     * @param bool $disabled
     * @return $this
     */
    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function getType(): int
    {
        return Component::TYPE_SELECT_MENU;
    }

    public function toArray(): array
    {
        return $this->toMergedArray([
            'options' => array_map(function (SelectOptionObject $option): array {
                return $option->toArray();
            }, $this->options),
            'placeholder' => $this->placeholder,
            'min_values' => $this->minValues,
            'max_values' => $this->maxValues,
            'disabled' => $this->disabled,
        ]);
    }

    final public function onInteract(ParameterBag $interactionRequest): void
    {
        $components = $interactionRequest->get('components');
        $submittedComponents = [];
        foreach ($components as $component) {
            $submittedComponents[] = new SelectOptionObject($component['label'], $component['value']);
        }
        $this->onMenuItemsSubmitted($submittedComponents, $interactionRequest);
    }

    /**
     * @param array<SelectOptionObject> $submittedComponents
     * @return void
     */
    abstract public function onMenuItemsSubmitted(array $submittedComponents, ParameterBag $interactionRequest): void;
}
