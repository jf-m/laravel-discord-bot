<?php

namespace Nwilging\LaravelDiscordBot\Support\Interactions\Responses;

use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;

class DiscordInteractionReplyResponse extends DiscordInteractionResponse
{
    public function __construct(?string $text, ?int $status = 200)
    {
        parent::__construct(DiscordComponent::REPLY_TO_MESSAGE, $text ? ['content' => $text] : null, $status);
    }
}