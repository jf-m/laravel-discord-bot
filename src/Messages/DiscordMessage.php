<?php

namespace Nwilging\LaravelDiscordBot\Messages;

class DiscordMessage
{
    public string $channelId;
    public ?array $options = null;
    public ?array $embeds = null;
    public ?array $components = null;
    public ?string $message = null;

    /**
     * @param string $channelId
     * @return $this
     */
    public function channelId(string $channelId) {
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
     * @param array|null $options
     * @return $this
     */
    public function options(?array $options) {
        $this->options = $options;
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
}