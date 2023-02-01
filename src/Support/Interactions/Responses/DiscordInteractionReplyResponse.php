<?php

namespace Nwilging\LaravelDiscordBot\Support\Interactions\Responses;

use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;

class DiscordInteractionReplyResponse extends DiscordInteractionResponse
{

    protected ?string $content = null;
    protected array $embeds = [];
    protected array $components = [];

    public function __construct(?string $content, array $embeds = [], $components = [], int $status = 200)
    {
        $this->content = $content;
        $this->embeds = $embeds;
        $this->components = $components;
        parent::__construct(DiscordComponent::REPLY_TO_MESSAGE, $status);
    }

    public function getData(): ?array
    {
        return array_filter([
            'content' => $this->content,
            'embeds' => array_map(function (Embed $embed): array {
                return $embed->toArray();
            }, $this->embeds),
            'components' => array_map(function (DiscordComponent $component): array {
                return $component->toArray();
            }, $this->components)
        ]);
    }

}