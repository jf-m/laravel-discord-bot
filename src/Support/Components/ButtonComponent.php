<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;

use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;

/**
 * Action Button InteractableComponent
 * @see https://discord.com/developers/docs/interactions/message-components#buttons
 */
abstract class ButtonComponent extends GenericButtonInteractableComponent
{
    use MergesArrays;

    public function __construct(string $label, mixed $parameter = null)
    {
        parent::__construct(static::STYLE_PRIMARY, $label, $parameter);
    }

    /**
     * Sets the button style to primary
     *
     * @see https://discord.com/developers/docs/interactions/message-components#button-object-button-styles
     * @return $this
     */
    public function withPrimaryStyle(): static
    {
        $this->style = static::STYLE_PRIMARY;
        return $this;
    }

    /**
     * Sets the button style to secondary
     *
     * @see https://discord.com/developers/docs/interactions/message-components#button-object-button-styles
     * @return $this
     */
    public function withSecondaryStyle(): static
    {
        $this->style = static::STYLE_SECONDARY;
        return $this;
    }

    /**
     * Sets the button style to success
     *
     * @see https://discord.com/developers/docs/interactions/message-components#button-object-button-styles
     * @return $this
     */
    public function withSuccessStyle(): static
    {
        $this->style = static::STYLE_SUCCESS;
        return $this;
    }

    /**
     * Sets the button style to danger
     *
     * @see https://discord.com/developers/docs/interactions/message-components#button-object-button-styles
     * @return $this
     */
    public function withDangerStyle(): static
    {
        $this->style = static::STYLE_DANGER;
        return $this;
    }
}
