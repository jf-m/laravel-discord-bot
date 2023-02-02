<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;


use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Objects\SelectOptionObject;
use Nwilging\LaravelDiscordBot\Support\Traits\HasDiscordInteractions;

/**
 * Select Menu InteractableComponent
 * @see https://discord.com/developers/docs/interactions/message-components#select-menu-object-select-menu-structure
 */
abstract class SelectMenuInteractableComponent implements DiscordInteractableComponent
{
    use HasDiscordInteractions;

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
    public function __construct(array $options = [], ?string $parameter = null)
    {
        $this->parameter = $parameter;
        $this->options = $options;
    }

    /**
     * Add an option to the select menu
     *
     * @param SelectOptionObject $option
     * @return $this
     */
    public function addOption(SelectOptionObject $option): self
    {
        $this->options[] = $option;
        return $this;
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
        return DiscordComponent::TYPE_SELECT_MENU;
    }

    public function toArray(): array
    {
        return array_filter([
            'options' => array_map(function (SelectOptionObject $option): array {
                return $option->toArray();
            }, $this->options),
            'custom_id' => $this->getCustomId(),
            'type' => $this->getType(),
            'placeholder' => $this->placeholder,
            'min_values' => $this->minValues,
            'max_values' => $this->maxValues,
            'disabled' => $this->disabled,
        ]);
    }

    public function populateFromInteractionRequest(array $interactionRequest): void {
        $values = $interactionRequest['data']['values'] ?? [];
        $submittedComponents = [];
        foreach ($values as $value) {
            $submittedComponents[] = new SelectOptionObject($value, $value);
        }
        $this->options = $submittedComponents;
    }

    final public function onInteract(array $interactionRequest): void
    {
        $this->onMenuItemsSubmitted($interactionRequest['data']['values'] ?? [], $interactionRequest);
    }

    /**
     * @param array<SelectOptionObject> $submittedValues
     * @return void
     */
    abstract public function onMenuItemsSubmitted(array $submittedValues, array $interactionRequest): void;
}
