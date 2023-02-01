<?php

namespace Nwilging\LaravelDiscordBot\Contracts\Support;

use Nwilging\LaravelDiscordBot\Support\Interactions\InteractionHandler;

interface DiscordComponent
{
    public const TYPE_ACTION_ROW = 1;
    public const TYPE_BUTTON = 2;
    public const TYPE_SELECT_MENU = 3;
    public const TYPE_TEXT_INPUT = 4;

    public const REPLY_WITH_MODAL = InteractionHandler::RESPONSE_TYPE_MODAL;
    public const DEFER_WHILE_HANDLING = InteractionHandler::RESPONSE_TYPE_DEFERRED_UPDATE_MESSAGE;
    public const REPLY_TO_MESSAGE = InteractionHandler::RESPONSE_TYPE_CHANNEL_MESSAGE_WITH_SOURCE;
    public const LOAD_WHILE_HANDLING = InteractionHandler::RESPONSE_TYPE_DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE;

    function getType(): int;

    public function toArray(): array;

    public function validate(): void;
}