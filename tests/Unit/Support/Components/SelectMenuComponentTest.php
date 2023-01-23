<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Components;

use Nwilging\LaravelDiscordBot\Jobs\DiscordInteractionHandlerJob;
use Nwilging\LaravelDiscordBot\Support\Component;
use Nwilging\LaravelDiscordBot\Support\InteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Components\SelectMenuInteractableComponent;
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

        $component = $this->getMockBuilder(SelectMenuInteractableComponent::class)->onlyMethods(['onMenuItemsSubmitted'])->setConstructorArgs([[$option1, $option2]])->getMock();

        $this->assertArraySubset([
            'type' => Component::TYPE_SELECT_MENU,
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

        $component = $this->getMockBuilder(SelectMenuInteractableComponent::class)->onlyMethods(['onMenuItemsSubmitted'])->setConstructorArgs([[$option1, $option2]])->getMock();
        $component->withPlaceholder('test placeholder');
        $component->withMinValues(5);
        $component->withMaxValues(10);
        $component->disabled();

        $this->assertArraySubset([
            'type' => Component::TYPE_SELECT_MENU,
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
        $interactionRequest = ['data' => ['components' => [
            ['value' => $selectedOptionOne, 'label' => $selectedOptionOne],
            ['value' => $selectedOptionTwo, 'label' => $selectedOptionTwo],
        ], 'id' => '1']];

        $component = $this->getMockBuilder(SelectMenuInteractableComponent::class)->onlyMethods(['onMenuItemsSubmitted'])->setConstructorArgs([[]])->getMock();
        $component->expects($this->once())
            ->method('onMenuItemsSubmitted')
            ->with([new SelectOptionObject($selectedOptionOne, $selectedOptionOne),new SelectOptionObject($selectedOptionTwo, $selectedOptionTwo)], $interactionRequest);
        $job = new DiscordInteractionHandlerJob($interactionRequest, $component);
        $job->handle();
    }
}
