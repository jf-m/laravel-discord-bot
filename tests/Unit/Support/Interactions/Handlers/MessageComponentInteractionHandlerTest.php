<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Interactions\Handlers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Mockery\MockInterface;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Jobs\DiscordInteractionHandlerJob;
use Nwilging\LaravelDiscordBot\Services\DiscordInteractionService;
use Nwilging\LaravelDiscordBot\Support\Components\ButtonComponent;
use Nwilging\LaravelDiscordBot\Support\Endpoints\ButtonInteractionEndpoint;
use Nwilging\LaravelDiscordBot\Support\Interactions\Handlers\MessageComponentInteractionHandler;
use Nwilging\LaravelDiscordBot\Support\Interactions\InteractionHandler;
use Nwilging\LaravelDiscordBot\Support\Interactions\Responses\DiscordInteractionReplyResponse;
use Nwilging\LaravelDiscordBotTests\TestCase;

class MessageComponentInteractionHandlerTest extends TestCase
{
    protected string $defaultBehavior = 'defer';

    protected MockInterface $laravel;

    public function setUp(): void
    {
        parent::setUp();

        $this->laravel = \Mockery::mock(Application::class);
    }

    public function testHandleDispatchesToJobAndReturnsCustomResponse()
    {
        $customId = 'testCustomId';
        $token = 'token-xxx';
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'token' => $token, 'data' => $customIdParam];
        $componentMock = \Mockery::mock(DiscordInteractionService::class);

        Bus::fake([
            DiscordInteractionHandlerJob::class
        ]);
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);


        $customEndpoint = $this->createPartialMock(ButtonInteractionEndpoint::class, ['getInteractionResponse', 'onClick']);
        $customEndpoint->expects($this->once())->method('getInteractionResponse')->willReturn(new DiscordInteractionReplyResponse('custom reply'));
        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId, $token)->andReturn($customEndpoint);

        $handler = new MessageComponentInteractionHandler('invalid', $this->laravel, $componentMock);
        $result = $handler->handle($request);
        $this->assertEquals(200, $result->getStatus());
        $this->assertArraySubset([
            'type' => DiscordComponent::REPLY_TO_MESSAGE,
            'data' => [
                'content' => 'custom reply',
            ],
        ], $result->toArray());
        Bus::assertDispatched(DiscordInteractionHandlerJob::class, function (DiscordInteractionHandlerJob $job) use ($parameterBag): bool {
            $this->assertSame($parameterBag, $job->data);
            return true;
        });
    }

    public function testHandleDispatchesToJobAndReturnsDefaultBehaviorResponse()
    {
        $customId = 'testCustomId';
        $token = 'token-xxx';
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'token' => $token, 'data' => $customIdParam];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $endpoint = $this->createPartialMock(ButtonInteractionEndpoint::class, ['onClick']);
        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId, $token)->andReturn($endpoint);

        Bus::fake([
            DiscordInteractionHandlerJob::class
        ]);
        $handler = new MessageComponentInteractionHandler(InteractionHandler::BEHAVIOR_DEFER, $this->laravel, $componentMock);
        $result = $handler->handle($request);
        $this->assertEquals(200, $result->getStatus());
        $this->assertArraySubset([
            'type' => DiscordComponent::DEFER_WHILE_HANDLING,
        ], $result->toArray());
        Bus::assertDispatched(DiscordInteractionHandlerJob::class, function (DiscordInteractionHandlerJob $job) use ($parameterBag): bool {
            $this->assertSame($parameterBag, $job->data);
            return true;
        });
    }

    public function testHandleDispatchesToJobAndReturnsDefaultBehaviorResponseLoad()
    {
        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $customId = 'testCustomId';
        $token = 'token-xxx';
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'token' => $token, 'data' => $customIdParam];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $endpoint = $this->createPartialMock(ButtonInteractionEndpoint::class, ['onClick']);

        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId, $token)->andReturn($endpoint);

        $job = \Mockery::mock(DiscordInteractionHandlerJob::class);
        Bus::fake([
            DiscordInteractionHandlerJob::class
        ]);

        $handler = new MessageComponentInteractionHandler(InteractionHandler::BEHAVIOR_LOAD, $this->laravel, $componentMock);
        $result = $handler->handle($request);
        $this->assertEquals(200, $result->getStatus());
        $this->assertArraySubset([
            'type' => DiscordComponent::LOAD_WHILE_HANDLING,
        ], $result->toArray());
        Bus::assertDispatched(DiscordInteractionHandlerJob::class, function (DiscordInteractionHandlerJob $job) use ($parameterBag): bool {
            $this->assertSame($parameterBag, $job->data);
            return true;
        });
    }

    public function testHandleDispatchesToJobAndReturnsDeferWhenNoValidDefaultBehavior()
    {
        $customId = 'testCustomId';
        $token = 'token-xxx';
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'token' => $token, 'data' => $customIdParam];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $endpoint = $this->createPartialMock(ButtonInteractionEndpoint::class, ['onClick']);
        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId, $token)->andReturn($endpoint);

        Bus::fake([
            DiscordInteractionHandlerJob::class
        ]);
        $handler = new MessageComponentInteractionHandler('invalid', $this->laravel, $componentMock);
        $result = $handler->handle($request);
        $this->assertEquals(200, $result->getStatus());
        $this->assertArraySubset([
            'type' => DiscordComponent::DEFER_WHILE_HANDLING,
        ], $result->toArray());
        Bus::assertDispatched(DiscordInteractionHandlerJob::class, function (DiscordInteractionHandlerJob $job) use ($parameterBag): bool {
            $this->assertSame($parameterBag, $job->data);
            return true;
        });
    }

    public function testHandleDispatchesToJobAsynchronously()
    {
        $customId = 'testCustomId';
        $token = 'token-xxx';
        $parameterBag = ['id' => '1', 'token' => $token, 'data' => ['custom_id' => $customId]];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $customEndpoint = $this->getMockBuilder(ButtonInteractionEndpoint::class)->onlyMethods(['onClick', 'shouldDispatchSync'])->getMock();
        $customEndpoint->expects($this->once())->method('shouldDispatchSync')->willReturn(false);

        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId, $token)->andReturn($customEndpoint);

        Bus::fake([
            DiscordInteractionHandlerJob::class
        ]);
        $handler = new MessageComponentInteractionHandler(InteractionHandler::BEHAVIOR_DEFER, $this->laravel, $componentMock);
        $result = $handler->handle($request);
        Bus::assertNotDispatchedSync(DiscordInteractionHandlerJob::class);
    }

    public function testHandleDispatchesToJobSynchronously()
    {
        $token = 'token-xxx';
        $customId = 'testCustomId';
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'token' => $token, 'data' => $customIdParam];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $customEndpoint = $this->getMockBuilder(ButtonInteractionEndpoint::class)->onlyMethods(['onClick', 'shouldDispatchSync'])->getMock();
        $customEndpoint->expects($this->once())->method('shouldDispatchSync')->willReturn(true);
        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId, $token)->andReturn($customEndpoint);

        Bus::fake([
            DiscordInteractionHandlerJob::class
        ]);
        $handler = new MessageComponentInteractionHandler(InteractionHandler::BEHAVIOR_DEFER, $this->laravel, $componentMock);
        $result = $handler->handle($request);
        Bus::assertDispatchedSync(DiscordInteractionHandlerJob::class);
    }
}
