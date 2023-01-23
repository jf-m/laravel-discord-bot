<?php

namespace Nwilging\LaravelDiscordBot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Foundation\Application;
use Nwilging\LaravelDiscordBot\Support\InteractableComponent;
use Symfony\Component\HttpFoundation\ParameterBag;

class DiscordInteractionHandlerJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, Queueable;

    protected InteractableComponent $component;
    public array $data;

    public function __construct(array $interactionRequest, InteractableComponent $component)
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