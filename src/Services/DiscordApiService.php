<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Services;

use GuzzleHttp\ClientInterface;
use Nwilging\LaravelDiscordBot\Contracts\Services\DiscordApiServiceContract;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;
use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;
use Nwilging\LaravelDiscordBot\Support\Embed;
use Psr\Http\Message\ResponseInterface;

class DiscordApiService implements DiscordApiServiceContract
{
    protected ?string $token;

    protected ?string $apiUrl;

    protected ?string $applicationId;

    protected ClientInterface $httpClient;

    public function __construct(?string $token, ?string $applicationId, ?string $apiUrl, ClientInterface $httpClient)
    {
        $this->token = $token;
        $this->apiUrl = $apiUrl;
        $this->httpClient = $httpClient;
        $this->applicationId = $applicationId;
    }

    public function sendMessage(DiscordMessage $discordMessage): array
    {
        $response = $this->makeRequest(
            'POST',
            sprintf('channels/%s/messages', $discordMessage->channelId),
            $discordMessage->toPayload()
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function sendFollowupMessage(DiscordMessage $discordMessage, DiscordInteractableComponent $component): array
    {
        $response = $this->makeRequest(
            'POST',
            sprintf('/webhooks/%s/%s', $this->applicationId, $component->getToken()),
            $discordMessage->toPayload()
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function editInitialInteractionResponse(DiscordMessage $discordMessage, DiscordInteractableComponent $component): array
    {
        $response = $this->makeRequest(
            'PATCH',
            sprintf('/webhooks/%s/%s/messages/@original', $this->applicationId, $component->getToken()),
            $discordMessage->toPayload()
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function deleteInitialInteractionResponse(DiscordInteractableComponent $component): array
    {
        $response = $this->makeRequest(
            'DELETE',
            sprintf('/webhooks/%s/%s/messages/@original', $this->applicationId, $component->getToken())
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function makeRequest(string $method, string $endpoint, array $payload = [], array $queryString = []): ResponseInterface
    {
        $url = sprintf('%s/%s', $this->apiUrl, $endpoint);

        return $this->httpClient->request($method, $url, [
            'headers' => [
                'Authorization' => sprintf('Bot %s', $this->token),
            ],
            'json' => $payload,
        ]);
    }
}
