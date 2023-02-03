<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Services;

use GuzzleHttp\ClientInterface;
use Mockery\MockInterface;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;
use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;
use Nwilging\LaravelDiscordBot\Services\DiscordApiService;
use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBotTests\TestCase;
use Psr\Http\Message\ResponseInterface;

class DiscordApiServiceTest extends TestCase
{
    protected string $token = 'test-token';

    protected string $apiUrl = 'https://example.com';

    protected string $applicationId = 'xxxxx';

    protected MockInterface $httpClient;

    protected DiscordApiService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClient = \Mockery::mock(ClientInterface::class);
        $this->service = new DiscordApiService($this->token, $this->applicationId, $this->apiUrl, $this->httpClient);
    }

    public function testSendTextMessage()
    {
        $channelId = '12345';
        $message = 'test message';

        $expectedRequestPayload = [
            'content' => $message,
        ];

        $expectedResponse = [
            'id' => '54321',
        ];

        $responseMock = \Mockery::mock(ResponseInterface::class);
        $responseMock->shouldAllowMockingMethod('getContents');

        $responseMock->shouldReceive('getBody')->once()->andReturnSelf();
        $responseMock->shouldReceive('getContents')->once()->andReturn(json_encode($expectedResponse));

        $this->httpClient->shouldReceive('request')
            ->once()
            ->with('POST', sprintf('%s/channels/%s/messages', $this->apiUrl, $channelId), [
                'headers' => [
                    'Authorization' => 'Bot ' . $this->token,
                ],
                'json' => $expectedRequestPayload,
            ])
            ->andReturn($responseMock);

        $result = $this->service->sendMessage((new DiscordMessage())->channelId($channelId)->message($message));
        $this->assertEquals($expectedResponse, $result);
    }

    public function testSendRichTextMessage()
    {
        $channelId = '12345';

        $expectedEmbed1Array = ['k1' => 'v1'];
        $expectedEmbed2Array = ['k2' => 'v2'];

        $expectedComponent1Array = ['k3' => 'v3'];
        $expectedComponent2Array = ['k4' => 'v4'];

        $embed1 = \Mockery::mock(Embed::class);
        $embed2 = \Mockery::mock(Embed::class);

        $component1 = \Mockery::mock(DiscordInteractableComponent::class);
        $component2 = \Mockery::mock(DiscordInteractableComponent::class);

        $embed1->shouldReceive('toArray')->andReturn($expectedEmbed1Array);
        $embed2->shouldReceive('toArray')->andReturn($expectedEmbed2Array);
        $component1->shouldReceive('toArray')->andReturn($expectedComponent1Array);
        $component2->shouldReceive('toArray')->andReturn($expectedComponent2Array);
        $component1->shouldReceive('validate')->andReturn(null);
        $component2->shouldReceive('validate')->andReturn(null);

        $expectedRequestPayload = [
            'embeds' => [$expectedEmbed1Array, $expectedEmbed2Array],
            'components' => [$expectedComponent1Array, $expectedComponent2Array],
        ];

        $expectedResponse = [
            'id' => '54321',
        ];

        $responseMock = \Mockery::mock(ResponseInterface::class);
        $responseMock->shouldAllowMockingMethod('getContents');

        $responseMock->shouldReceive('getBody')->andReturnSelf();
        $responseMock->shouldReceive('getContents')->andReturn(json_encode($expectedResponse));

        $this->httpClient->shouldReceive('request')
            ->once()
            ->with('POST', sprintf('%s/channels/%s/messages', $this->apiUrl, $channelId), [
                'headers' => [
                    'Authorization' => 'Bot ' . $this->token,
                ],
                'json' => $expectedRequestPayload,
            ])
            ->andReturn($responseMock);

        $result = $this->service->sendMessage((new DiscordMessage())->channelId($channelId)->embeds([$embed1, $embed2])->components([$component1, $component2]));
        $this->assertEquals($expectedResponse, $result);
    }
}
