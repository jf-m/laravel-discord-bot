<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;

use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Link Button InteractableComponent
 * @see https://discord.com/developers/docs/interactions/message-components#buttons
 */
class LinkButtonComponent extends GenericButtonInteractableComponent
{
    use MergesArrays;

    protected string $url;

    public function __construct(string $label, string $url, ?string $parameter = null)
    {
        parent::__construct(static::STYLE_LINK, $label, $parameter);
        $this->url = $url;
    }

    public function onClicked(ParameterBag $interactionRequest): void
    {

    }

    public function toArray(): array
    {
        return $this->toMergedArray([
            'url' => $this->url,
        ]);
    }
}
