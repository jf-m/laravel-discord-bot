<?php

namespace Nwilging\LaravelDiscordBot\Messages;

class PlainDiscordMessage extends DiscordMessage
{
    public ?string $message = null;

    /**
     * @param string|null $message
     * @return $this
     */
    public function message(?string $message)
    {
        $this->message = $message;
        return $this;
    }
}