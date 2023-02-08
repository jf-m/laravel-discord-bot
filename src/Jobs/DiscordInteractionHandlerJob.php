<?php

namespace Nwilging\LaravelDiscordBot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Endpoints\InteractionEndpoint;

class DiscordInteractionHandlerJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, Queueable;

    protected InteractionEndpoint $endpoint;
    public array $data;

    public function __construct(array $interactionRequest, InteractionEndpoint $endpoint)
    {
        $this->data = $interactionRequest;
        $this->endpoint = $endpoint;
        $this->onConnection($this->endpoint->interactOnConnection ?: config('discord.interactions.default_connection'));
        $this->onQueue($this->endpoint->interactOnQueue ?: config('discord.interactions.default_queue'));
    }

    public function handle(): void
    {
        $this->endpoint->onInteract($this->data);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->data['id'];
    }
}