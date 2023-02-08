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
use Nwilging\LaravelDiscordBot\Support\Interactions\Responses\DiscordInteractionModalResponse;
use Symfony\Component\HttpFoundation\ParameterBag;

class MessageComponentInteractionHandler extends InteractionHandler
{
    protected string $defaultBehavior;

    protected Application $laravel;

    protected DiscordInteractionService $discordInteractionService;

    public function __construct(string $defaultBehavior, Application $laravel, DiscordInteractionService $discordInteractionService)
    {
        $this->defaultBehavior = in_array($defaultBehavior, [InteractionHandler::BEHAVIOR_LOAD, InteractionHandler::BEHAVIOR_DEFER]) ? $defaultBehavior : InteractionHandler::BEHAVIOR_DEFER;
        $this->discordInteractionService = $discordInteractionService;
        $this->laravel = $laravel;
    }

    public function handle(Request $request): DiscordInteractionResponse
    {
        $requestData = $request->all();
        /** @var ParameterBag $data */
        $data = $requestData['data'] ?? null;
        if ($data && $customId = $data['custom_id'] ?? null) {
            $endpoint = $this->discordInteractionService->getComponentFromCustomId($customId, $requestData['token']);
            $endpoint->populateFromInteractionRequest($requestData);
            if ($endpoint->shouldDispatchSync()) {
                DiscordInteractionHandlerJob::dispatchSync($requestData, $endpoint);
            } else {
                DiscordInteractionHandlerJob::dispatch($requestData, $endpoint);
            }
            if ($response = $endpoint->getInteractionResponse($requestData)) {
                if ($response instanceof DiscordInteractionModalResponse) {
                    $response->prepareCustomId($endpoint);
                }
                $response->validate();
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
