<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Components;

use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Jobs\DiscordInteractionHandlerJob;
use Nwilging\LaravelDiscordBot\Support\Components\SelectMenuInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Endpoints\SelectMenuInteractionEndpoint;
use Nwilging\LaravelDiscordBot\Support\Objects\SelectOptionObject;
use Nwilging\LaravelDiscordBotTests\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

class SelectMenuComponentTest extends TestCase
{
    public function testComponent()
    {
        $expectedOption1Array = ['k1' => 'v1'];
        $expectedOption2Array = ['k2' => 'v2'];

        $option1 = \Mockery::mock(SelectOptionObject::class);
        $option2 = \Mockery::mock(SelectOptionObject::class);

        $option1->shouldReceive('toArray')->andReturn($expectedOption1Array);
        $option2->shouldReceive('toArray')->andReturn($expectedOption2Array);

        $component = new SelectMenuInteractableComponent([$option1, $option2]);

        $this->assertArraySubset([
            'type' => DiscordComponent::TYPE_SELECT_MENU,
            'options' => [$expectedOption1Array, $expectedOption2Array],
        ], $component->toArray());
    }

    public function testComponentWithOptions()
    {
        $expectedOption1Array = ['k1' => 'v1'];
        $expectedOption2Array = ['k2' => 'v2'];

        $option1 = \Mockery::mock(SelectOptionObject::class);
        $option2 = \Mockery::mock(SelectOptionObject::class);

        $option1->shouldReceive('toArray')->andReturn($expectedOption1Array);
        $option2->shouldReceive('toArray')->andReturn($expectedOption2Array);

        $component = new SelectMenuInteractableComponent([$option1, $option2]);
        $component->withPlaceholder('test placeholder');
        $component->withMinValues(5);
        $component->withMaxValues(10);
        $component->disabled();

        $this->assertArraySubset([
            'type' => DiscordComponent::TYPE_SELECT_MENU,
            'options' => [$expectedOption1Array, $expectedOption2Array],
            'placeholder' => 'test placeholder',
            'min_values' => 5,
            'max_values' => 10,
            'disabled' => true,
        ], $component->toArray());
    }

    public function testComponentInteraction()
    {
        $selectedOptionOne = '1';
        $selectedOptionTwo = '5';
        $interactionRequest = ['data' => ['values' => [$selectedOptionOne, $selectedOptionTwo], 'id' => '1']];

        $component = new SelectMenuInteractableComponent([]);
        $endpoint = $this->getMockBuilder(SelectMenuInteractionEndpoint::class)->onlyMethods(['onMenuItemsSubmit'])->getMock();
        $component->withEndpoint($endpoint);
        $endpoint->expects($this->once())
            ->method('onMenuItemsSubmit')
            ->with([$selectedOptionOne, $selectedOptionTwo], $interactionRequest);
        $job = new DiscordInteractionHandlerJob($interactionRequest, $endpoint);
        $job->handle();
    }
}
