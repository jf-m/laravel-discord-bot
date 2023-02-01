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
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'data' => $customIdParam];
        $componentMock = \Mockery::mock(DiscordInteractionService::class);

        Bus::fake([
            DiscordInteractionHandlerJob::class
        ]);
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);


        $customButton = $this->createPartialMock(ButtonComponent::class, ['getInteractionResponse', 'onClicked']);
        $customButton->expects($this->once())->method('getInteractionResponse')->willReturn(new DiscordInteractionReplyResponse('custom reply'));
        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId)->andReturn($customButton);

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
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'data' => $customIdParam];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $customButton = $this->createPartialMock(ButtonComponent::class, ['onClicked']);
        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId)->andReturn($customButton);

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
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'data' => $customIdParam];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $customButton = $this->createPartialMock(ButtonComponent::class, ['onClicked']);

        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId)->andReturn($customButton);

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
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'data' => $customIdParam];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $customButton = $this->createPartialMock(ButtonComponent::class, ['onClicked']);
        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId)->andReturn($customButton);

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
        $parameterBag = ['id' => '1', 'data' => ['custom_id' => $customId]];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $customButton = $this->getMockBuilder(ButtonComponent::class)->onlyMethods(['onClicked', 'shouldDispatchSync'])->setConstructorArgs([''])->getMock();
        $customButton->expects($this->once())->method('shouldDispatchSync')->willReturn(false);

        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId)->andReturn($customButton);

        Bus::fake([
            DiscordInteractionHandlerJob::class
        ]);
        $handler = new MessageComponentInteractionHandler(InteractionHandler::BEHAVIOR_DEFER, $this->laravel, $componentMock);
        $result = $handler->handle($request);
        Bus::assertNotDispatchedSync(DiscordInteractionHandlerJob::class);
    }

    public function testHandleDispatchesToJobSynchronously()
    {
        $customId = 'testCustomId';
        $customIdParam = ['custom_id' => $customId];
        $parameterBag = ['id' => '1', 'data' => $customIdParam];

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($parameterBag);

        $customButton = $this->getMockBuilder(ButtonComponent::class)->onlyMethods(['onClicked', 'shouldDispatchSync'])->setConstructorArgs([''])->getMock();
        $customButton->expects($this->once())->method('shouldDispatchSync')->willReturn(true);

        $componentMock = \Mockery::mock(DiscordInteractionService::class);
        $componentMock->shouldReceive('getComponentFromCustomId')->with($customId)->andReturn($customButton);

        Bus::fake([
            DiscordInteractionHandlerJob::class
        ]);
        $handler = new MessageComponentInteractionHandler(InteractionHandler::BEHAVIOR_DEFER, $this->laravel, $componentMock);
        $result = $handler->handle($request);
        Bus::assertDispatchedSync(DiscordInteractionHandlerJob::class);
    }
}
