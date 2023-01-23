<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Mockery\Exception;
use Nwilging\LaravelDiscordBot\Contracts\Services\DiscordApiServiceContract;
use Nwilging\LaravelDiscordBot\Support\Component;
use Nwilging\LaravelDiscordBot\Support\InteractableComponent;
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

    public function sendMessage(string $channelId, ?string $message = null, ?array $embeds = null, ?array $components = null, ?array $options = null): array
    {
        $payload = [];

        if ($embeds) {
            $payload['embeds'] = array_map(function (Embed $embed): array {
                return $embed->toArray();
            }, $embeds);
        }

        if ($components) {
            $payload['components'] = array_map(function (Component $component): array {
                return $component->toArray();
            }, $components);
        }

        if ($message) {
            $payload['content'] = $message;
        }

        if ($options) {
            $payload = array_merge($this->buildMessageOptions($options), $payload);
        }

        $response = $this->makeRequest(
            'POST',
            sprintf('channels/%s/messages', $channelId),
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
