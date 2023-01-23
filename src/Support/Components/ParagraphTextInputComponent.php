<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;

use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Paragraph Text Input InteractableComponent
 * @see https://discord.com/developers/docs/interactions/message-components#text-inputs-text-input-structure
 */
abstract class ParagraphTextInputComponent extends GenericTextInputInteractableComponent
{
    use MergesArrays;

    public function __construct(string $label, ?string $parameter = null)
    {
        parent::__construct(static::STYLE_PARAGRAPH, $label, $parameter);
    }
}
