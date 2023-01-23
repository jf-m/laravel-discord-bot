<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support;

use Illuminate\Contracts\Queue\ShouldQueue;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Abstract Message InteractableComponent
 * @see https://discord.com/developers/docs/interactions/message-components#component-object
 */
abstract class InteractableComponent extends Component
{

    public ?string $parameter = null;

    public ?string $interactOnQueue = null;
    public ?string $interactOnConnection = null;

    /**
     * @param string|null $parameter
     */
    public function __construct(?string $parameter)
    {
        $this->parameter = $parameter;
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return null;
    }

    abstract public function onInteract(array $interactionRequest): void;

    /**
     * @throws \Exception
     */
    protected function getCustomId(): string
    {
        $className = get_class($this);
        $className = str_replace(config('discord.interactions.namespace'), '', $className);
        $c = json_encode([
            $className,
            $this->parameter
        ], \JSON_UNESCAPED_UNICODE);
        if (strlen($c) > 100) {
            throw new \Exception(sprintf("Discord does not allow a payload of more than 100 characters. Reduce the length of your custom parameter or the classname of this component. Classname length: %s, parameter length: %s.", strlen($className), strlen($this->parameter)));
        }

        return $c;
    }

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
