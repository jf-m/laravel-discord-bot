<?php

namespace Nwilging\LaravelDiscordBot\Messages;

abstract class DiscordMessage
{
    public string $channelId;
    public ?array $options = null;

    /**
     * @param string $channelId
     * @return $this
     */
    public function channelId(string $channelId) {
        $this->channelId = $channelId;
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

}