<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;

use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Paragraph Text Input InteractableComponent
 * @see https://discord.com/developers/docs/interactions/message-components#text-inputs-text-input-structure
 */
class ParagraphTextInputComponent extends GenericTextInputInteractableComponent
{
    public function __construct(string $label, mixed $parameter = null)
    {
        parent::__construct(static::STYLE_PARAGRAPH, $label, $parameter);
    }
}
