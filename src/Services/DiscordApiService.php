<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Services;

use GuzzleHttp\ClientInterface;
use Nwilging\LaravelDiscordBot\Contracts\Services\DiscordApiServiceContract;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;
use Nwilging\LaravelDiscordBot\Support\Embed;
use Psr\Http\Message\ResponseInterface;

class DiscordApiService implements DiscordApiServiceContract
{
    protected string $token;

    protected string $apiUrl;

    protected ClientInterface $httpClient;

    public function __construct(string $token, string $apiUrl, ClientInterface $httpClient)
    {
        $this->token = $token;
        $this->apiUrl = $apiUrl;
        $this->httpClient = $httpClient;
    }

    public function sendMessage(DiscordMessage $discordMessage): array
    {
        $payload = [];
        if ($discordMessage->embeds) {
            $payload['embeds'] = array_map(function (Embed $embed): array {
                return $embed->toArray();
            }, $discordMessage->embeds);
        }

        if ($discordMessage->components) {
            $payload['components'] = array_map(function (DiscordComponent $component): array {
                $component->validate();
                return $component->toArray();
            }, $discordMessage->components);
        }

        if ($discordMessage->message()) {
            $payload['content'] = $discordMessage->message();
        }

        if ($discordMessage->options) {
            $payload = array_merge($this->buildMessageOptions($discordMessage->options), $payload);
        }

        $response = $this->makeRequest(
            'POST',
            sprintf('channels/%s/messages', $discordMessage->channelId),
            $payload
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function buildMessageOptions(array $options): array
    {
        return [];
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
