<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Endpoints;

use Illuminate\Contracts\Queue\ShouldQueue;
use Nwilging\LaravelDiscordBot\Facades\Discord;
use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBot\Support\Interactions\Responses\DiscordInteractionModalResponse;

abstract class InteractionEndpoint
{
    public mixed $actionValue = null;
    public mixed $actionName = null;
    public ?string $token = null;

    public ?string $interactOnQueue = null;
    public ?string $interactOnConnection = null;

    final public function __construct(mixed $actionValue = null, mixed $actionName = null)
    {
        $this->actionValue = $actionValue;
        $this->actionName = $actionName;
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return null;
    }

    public function getInteractionResponseForResponseModal(array $modalInputs, array $interactionRequest): ?DiscordInteractionResponse
    {
        return null;
    }

    public function sendFollowupMessage(DiscordMessage $discordMessage): array
    {
        return Discord::sendFollowupMessage($discordMessage, $this);
    }

    public function deleteInitialInteractionResponse(): array
    {
        return Discord::deleteInitialInteractionResponse($this);
    }

    public function editInitialInteractionResponse(DiscordMessage $discordMessage): array
    {
        return Discord::editInitialInteractionResponse($discordMessage, $this);
    }

    public function onResponseModalSubmitted(array $inputs, array $interactionRequest): void
    {
    }

    abstract public function onInteract(array $interactionRequest): void;

    public function populateFromInteractionRequest(array $interactionRequest): void
    {

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
        if ($this->interactOnConnection) {
            return false;
        }
        return !($this instanceof ShouldQueue);
    }

    /**
     * @throws \Exception
     */
    public function getCustomId(): ?string
    {
        $className = get_class($this);
        foreach (config('discord.interactions.namespaces', ['App\\']) as $namespace) {
            $truncClassName = str_replace($namespace, '', $className);
            if ($truncClassName != $className) {
                $className = $truncClassName;
                break;
            }
        }

        $toEncode = [$className];

        if ($this->actionValue !== null || $this->actionName !== null) {
            $toEncode[] = $this->actionValue;
            if ($this->actionName !== null) {
                $toEncode[] = $this->actionName;
            }
        }

        return json_encode($toEncode, \JSON_UNESCAPED_UNICODE);
    }
}
