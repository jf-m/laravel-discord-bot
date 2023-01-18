<?php

namespace Nwilging\LaravelDiscordBot\Messages;

class RichDiscordMessage extends DiscordMessage
{
    public ?array $embeds = null;
    public ?array $components = null;

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