<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support;

use Illuminate\Contracts\Support\Arrayable;
use Nwilging\LaravelDiscordBot\Support\Interactions\InteractionHandler;
use Nwilging\LaravelDiscordBot\Support\Traits\FiltersRecursive;

/**
 * Component Object
 * @see https://discord.com/developers/docs/interactions/message-components#component-object
 */
abstract class Component implements Arrayable
{
    use FiltersRecursive;

    public const TYPE_ACTION_ROW = 1;
    public const TYPE_BUTTON = 2;
    public const TYPE_TEXT_INPUT = 4;
    public const TYPE_SELECT_MENU = 3;
    public const LOAD_WHILE_HANDLING = InteractionHandler::RESPONSE_TYPE_DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE;
    public const DEFER_WHILE_HANDLING = InteractionHandler::RESPONSE_TYPE_DEFERRED_UPDATE_MESSAGE;
    public const REPLY_TO_MESSAGE = InteractionHandler::RESPONSE_TYPE_CHANNEL_MESSAGE_WITH_SOURCE;

    /**
     * Returns a Discord-API compliant component array
     *
     * @see https://discord.com/developers/docs/interactions/message-components#component-object
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->arrayFilterRecursive([
            'type' => $this->getType(),
        ]);
    }

    abstract protected function getType();
}
