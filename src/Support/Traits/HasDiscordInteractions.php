<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Traits;

use Nwilging\LaravelDiscordBot\Support\Endpoints\InteractionEndpoint;


trait HasDiscordInteractions
{
    protected ?InteractionEndpoint $interactionEndpoint = null;

    public function validate(): void
    {
        if ($this->interactionEndpoint) {
            $charLimit = config('discord.custom_id_character_limit');
            $customId = $this->interactionEndpoint->getCustomId();
            if ($customId && strlen($customId) > $charLimit) {
                throw new \Exception(sprintf("Discord does not allow a payload of more than %s characters. Reduce the length of your custom parameter or the classname of this component. Classname length: %s, parameter length: %s. https://discord.com/developers/docs/interactions/message-components#custom-id", $charLimit, strlen($customId), strlen($this->parameter)));
            }
        }
    }
    public function getCustomId(): ?string {
        return $this->interactionEndpoint?->getCustomId();
    }

    public function getInteractionEndpoint(): ?InteractionEndpoint
    {
        return $this->interactionEndpoint;
    }

}
