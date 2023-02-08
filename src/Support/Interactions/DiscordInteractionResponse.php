<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Interactions;

use Illuminate\Contracts\Support\Arrayable;
use Nwilging\LaravelDiscordBot\Support\Endpoints\InteractionEndpoint;

class DiscordInteractionResponse implements Arrayable
{
    protected int $status;

    protected int $type;

    public function __construct(int $type, ?int $status = 200)
    {
        $this->status = $status;
        $this->type = $type;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getData(): ?array
    {
        return null;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'data' => $this->getData(),
        ]);
    }

    public function validate(): void
    {

    }
}
