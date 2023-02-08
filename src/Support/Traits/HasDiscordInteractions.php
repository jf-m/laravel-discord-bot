<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Traits;

use Illuminate\Contracts\Queue\ShouldQueue;
use Nwilging\LaravelDiscordBot\Facades\Discord;
use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBot\Support\Interactions\Responses\GenericDiscordInteractionModalResponse;


trait HasDiscordInteractions
{
    public mixed $actionValue = null;
    public mixed $actionName = null;
    public ?string $token = null;

    public ?string $interactOnQueue = null;
    public ?string $interactOnConnection = null;

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return null;
    }

    public function withAction(mixed $actionValue, mixed $actionName = null): static
    {
        $this->actionName = $actionName;
        $this->actionValue = $actionValue;
        return $this;
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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function createResponseModal(string $title, array $inputComponents): DiscordInteractionResponse
    {
        return new GenericDiscordInteractionModalResponse($title, $inputComponents, $this->getCustomId());
    }

    public function onResponseModalSubmitted(GenericDiscordInteractionModalResponse $modal, array $interactionRequest): void
    {
    }

    public function getInteractionResponseForResponseModal(GenericDiscordInteractionModalResponse $modal, array $interactionRequest): ?DiscordInteractionResponse
    {
        return null;
    }

    abstract public function onInteract(array $interactionRequest): void;

    abstract public function populateFromInteractionRequest(array $interactionRequest): void;

    public function getActionName(): mixed
    {
        return $this->actionName;
    }

    public function getActionValue(): mixed
    {
        return $this->actionValue;
    }

    /**
     * @throws \Exception
     */
    protected function getCustomId(): ?string
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

    public function validate(): void
    {
        $charLimit = config('discord.custom_id_character_limit');
        $customId = $this->getCustomId();
        if ($customId && strlen($customId) > $charLimit) {
            throw new \Exception(sprintf("Discord does not allow a payload of more than %s characters. Reduce the length of your custom parameter or the classname of this component. Classname length: %s, parameter length: %s. https://discord.com/developers/docs/interactions/message-components#custom-id", $charLimit, strlen($customId), strlen($this->parameter)));
        }
    }
}
