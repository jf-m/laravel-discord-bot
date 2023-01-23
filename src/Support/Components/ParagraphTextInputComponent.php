<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;

use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Paragraph Text Input Component
 * @see https://discord.com/developers/docs/interactions/message-components#text-inputs-text-input-structure
 */
abstract class ParagraphTextInputComponent extends GenericTextInputComponent
{
    use MergesArrays;

    public function __construct(string $label, ?string $parameter = null)
    {
        parent::__construct(static::STYLE_PARAGRAPH, $label, $parameter);
    }
}
