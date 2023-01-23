<?php

namespace Nwilging\LaravelDiscordBot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Foundation\Application;
use Nwilging\LaravelDiscordBot\Support\Component;
use Symfony\Component\HttpFoundation\ParameterBag;

class DiscordInteractionHandlerJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected Component $component;
    public ParameterBag $data;

    public function __construct(ParameterBag $interactionRequest, Component $component)
    {
        $this->data = $interactionRequest;
        $this->component = $component;
    }

    public function onQueue(): mixed
    {
        return $this->component->interactOnQueue ?: config('discord.interactions.default_queue');
    }

    public function onConnection(): mixed
    {
        return $this->component->interactOnConnection ?: config('discord.interactions.default_connection');
    }

    public function handle(): void
    {
        $this->component->onInteract($this->data);
    }
}