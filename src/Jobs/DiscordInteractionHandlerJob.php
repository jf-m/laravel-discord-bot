<?php

namespace Nwilging\LaravelDiscordBot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Nwilging\LaravelDiscordBot\Support\Endpoints\InteractionEndpoint;

class DiscordInteractionHandlerJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, Queueable;

    protected InteractionEndpoint $endpoint;
    public array $data;
    public ?array $modalInputs;

    public function __construct(array $interactionRequest, InteractionEndpoint $endpoint, ?array $modalInputs = null)
    {
        $this->data = $interactionRequest;
        $this->endpoint = $endpoint;
        $this->onConnection($this->endpoint->interactOnConnection ?: config('discord.interactions.default_connection'));
        $this->onQueue($this->endpoint->interactOnQueue ?: config('discord.interactions.default_queue'));
        $this->modalInputs = $modalInputs;
    }

    public function handle(): void
    {
        if ($this->modalInputs) {
            $this->endpoint->onResponseModalSubmitted($this->modalInputs, $this->data);
        } else {
            $this->endpoint->onInteract($this->data);
        }
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