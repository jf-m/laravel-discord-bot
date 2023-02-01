<?php

namespace Nwilging\LaravelDiscordBot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;

class DiscordInteractionHandlerJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, Queueable;

    protected DiscordInteractableComponent $component;
    public array $data;

    public function __construct(array $interactionRequest, DiscordInteractableComponent $component)
    {
        $this->data = $interactionRequest;
        $this->component = $component;
        $this->onConnection($this->component->interactOnConnection ?: config('discord.interactions.default_connection'));
        $this->onQueue($this->component->interactOnQueue ?: config('discord.interactions.default_queue'));
    }

    public function handle(): void
    {
        $this->component->onInteract($this->data);
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