<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Interactions\Handlers;

use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Nwilging\LaravelDiscordBot\Jobs\DiscordInteractionHandlerJob;
use Nwilging\LaravelDiscordBot\Services\DiscordInteractionService;
use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBot\Support\Interactions\InteractionHandler;
use Symfony\Component\HttpFoundation\ParameterBag;

class MessageComponentInteractionHandler extends InteractionHandler
{
    protected string $defaultBehavior;

    protected Application $laravel;

    protected DiscordInteractionService $discordInteractionService;

    public function __construct(string $defaultBehavior, Application $laravel, DiscordInteractionService $discordInteractionService)
    {
        $this->defaultBehavior = $defaultBehavior;
        $this->discordInteractionService = $discordInteractionService;
        $this->laravel = $laravel;
    }

    public function handle(Request $request): DiscordInteractionResponse
    {
        $requestData = $request->all();
        /** @var ParameterBag $data */
        $data = $requestData['data'] ?? null;
        if ($data && $customId = $data['custom_id'] ?? null) {
            $component = $this->discordInteractionService->getComponentFromCustomId($customId);
            if ($component->shouldDispatchSync()) {
                DiscordInteractionHandlerJob::dispatchSync($requestData, $component);
            } else {
                DiscordInteractionHandlerJob::dispatch($requestData, $component);
            }
            if ($response = $component->getInteractionResponse($requestData)) {
                return $response;
            }

            switch ($this->defaultBehavior) {
                case static::BEHAVIOR_LOAD:
                    return new DiscordInteractionResponse(static::RESPONSE_TYPE_DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE);
                case static::BEHAVIOR_DEFER:
                    return new DiscordInteractionResponse(static::RESPONSE_TYPE_DEFERRED_UPDATE_MESSAGE);
            }

            return new DiscordInteractionResponse(static::RESPONSE_TYPE_DEFERRED_UPDATE_MESSAGE);
        } else {
            throw new \Exception('Discord Interaction received with missing custom_id');
        }
    }
}
