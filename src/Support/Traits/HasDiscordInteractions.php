<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Traits;

use Illuminate\Contracts\Queue\ShouldQueue;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Symfony\Component\HttpFoundation\ParameterBag;

trait HasDiscordInteractions
{
    public ?string $parameter = null;

    public ?string $interactOnQueue = null;
    public ?string $interactOnConnection = null;

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return null;
    }

    abstract public function onInteract(array $interactionRequest): void;

    public function getParameter(): ?string
    {
        return $this->parameter;
    }

    /**
     * @throws \Exception
     */
    protected function getCustomId(): ?string
    {
        $className = get_class($this);
        $className = str_replace(config('discord.interactions.namespace', 'App'), '', $className);

        return json_encode([
            $className,
            $this->parameter
        ], \JSON_UNESCAPED_UNICODE);
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

    public function validate(): void
    {
        $charLimit = config('discord.custom_id_character_limit');

        if (strlen($this->getCustomId()) > $charLimit) {
            throw new \Exception(sprintf("Discord does not allow a payload of more than %s characters. Reduce the length of your custom parameter or the classname of this component. Classname length: %s, parameter length: %s. https://discord.com/developers/docs/interactions/message-components#custom-id", $charLimit, strlen(get_class($this)), strlen($this->parameter)));
        }
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
        return [
            'custom_id' => $this->getCustomId(),
            'type' => $this->getType()
        ];
    }
}
