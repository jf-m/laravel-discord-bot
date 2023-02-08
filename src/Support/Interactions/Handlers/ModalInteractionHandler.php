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

class ModalInteractionHandler extends MessageComponentInteractionHandler
{

    public function handle(Request $request): DiscordInteractionResponse
    {
        $requestData = $request->all();
        /** @var ParameterBag $data */
        $data = $requestData['data'] ?? null;
        if ($data && $customId = $data['custom_id'] ?? null) {
            $endpoint = $this->discordInteractionService->getComponentFromCustomId($customId, $requestData['token']);
            $inputs = [];
            foreach ($data['components'][0]['components'] as $component) {
                $inputs[$component['custom_id']] = $component['value'];
            }
            $endpoint->populateFromInteractionRequest($requestData);
            if ($endpoint->shouldDispatchSync()) {
                DiscordInteractionHandlerJob::dispatchSync($requestData, $endpoint, $inputs);
            } else {
                DiscordInteractionHandlerJob::dispatch($requestData, $endpoint, $inputs);
            }
            if ($response = $endpoint->getInteractionResponseForResponseModal($inputs, $requestData)) {
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
