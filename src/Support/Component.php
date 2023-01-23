<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Arrayable;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBot\Support\Interactions\InteractionHandler;
use Nwilging\LaravelDiscordBot\Support\Traits\FiltersRecursive;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Abstract Message Component
 * @see https://discord.com/developers/docs/interactions/message-components#component-object
 */
abstract class Component implements Arrayable
{
    use FiltersRecursive;

    public const TYPE_ACTION_ROW = 1;
    public const TYPE_BUTTON = 2;
    public const TYPE_SELECT_MENU = 3;
    public const TYPE_TEXT_INPUT = 4;

    public const LOAD_WHILE_HANDLING = InteractionHandler::RESPONSE_TYPE_DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE;
    public const DEFER_WHILE_HANDLING = InteractionHandler::RESPONSE_TYPE_DEFERRED_UPDATE_MESSAGE;
    public const REPLY_TO_MESSAGE = InteractionHandler::RESPONSE_TYPE_CHANNEL_MESSAGE_WITH_SOURCE;

    public ?string $parameter = null;
    protected ?string $replyContent = null;
    public ?int $replyBehavior = null;

    public ?string $interactOnQueue = null;
    public ?string $interactOnConnection = null;

    /**
     * @param string|null $parameter
     */
    public function __construct(?string $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * Can be:
     * static::LOAD_WHILE_HANDLING; // Shows a loading message/status while handling
     * static::REPLY_TO_MESSAGE; // Replies to the interaction with replyContent(). Required if you want to reply to the interaction
     * static::DEFER_WHILE_HANDLING; // Shows no loading message/status while handling
     * @param int|null $replyBehavior
     * @param string|null $replyContent only used when $replyBehavior is REPLY_TO_MESSAGE
     * @return $this
     */
    public function withReplyBehavior(?int $replyBehavior, ?string $replyContent = null): static
    {
        $this->replyBehavior = $replyBehavior;
        $this->replyContent = $replyContent;
        return $this;
    }

    abstract public function onInteract(ParameterBag $interactionRequest): void;

    public function getDiscordInteractionResponse(): ?DiscordInteractionResponse
    {
        if ($this->replyBehavior !== null) {
            return new DiscordInteractionResponse($this->replyBehavior, $this->replyContent ? [
                'content' => $this->replyContent,
            ] : null);
        }
        return null;
    }

    /**
     * @throws \Exception
     */
    protected function getCustomId(): string
    {
        $className = get_class($this);
        $c = json_encode([
            $className,
            $this->parameter
        ], \JSON_UNESCAPED_UNICODE);
        if (strlen($c) > 100) {
            throw new \Exception(sprintf("Discord does not allow a payload of more than 100 characters. Reduce the length of your custom parameter or the classname of this component. Classname length: %s, parameter length: %s.", strlen($className), strlen($this->parameter)));
        }

        return $c;
    }

    abstract protected function getType();

    public function onQueue(string $queue): self
    {
        $this->interactOnQueue = $queue;
        return $this;
    }

    public function onConnection(string $connection): self
    {
        $this->interactOnConnection = $connection;
        return $this;
    }

    public function shouldDispatchSync(): bool
    {
        return !($this instanceof ShouldQueue);
    }

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
            'custom_id' => $this->getCustomId(),
            'type' => $this->getType(),
        ]);
    }
}
