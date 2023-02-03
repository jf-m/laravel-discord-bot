<?php

namespace Nwilging\LaravelDiscordBot\Messages;

use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Support\Embed;

class DiscordMessage
{
    public string $channelId;
    public ?array $embeds = null;
    public ?array $components = null;
    public ?string $message = null;

    /**
     * @param string $channelId
     * @return $this
     */
    public function channelId(string $channelId)
    {
        $this->channelId = $channelId;
        return $this;
    }

    /**
     * @param string|null $message
     * @return $this
     */
    public function message(?string $message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param array|null $embeds
     * @return $this
     */
    public function embeds(?array $embeds)
    {
        $this->embeds = $embeds;
        return $this;
    }

    /**
     * @param array|null $components
     * @return $this
     */
    public function components(?array $components)
    {
        $this->components = $components;
        return $this;
    }

    public function toPayload(): array
    {
        $payload = [];
        if ($this->embeds) {
            $payload['embeds'] = array_map(function (Embed $embed): array {
                return $embed->toArray();
            }, $this->embeds);
        }

        if ($this->components) {
            $payload['components'] = array_map(function (DiscordComponent $component): array {
                $component->validate();
                return $component->toArray();
            }, $this->components);
        }

        if ($this->message) {
            $payload['content'] = $this->message;
        }
        return $payload;
    }
}