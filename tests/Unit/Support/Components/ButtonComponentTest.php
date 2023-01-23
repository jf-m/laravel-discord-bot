<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Components;

use Illuminate\Http\Request;
use Nwilging\LaravelDiscordBot\Jobs\DiscordInteractionHandlerJob;
use Nwilging\LaravelDiscordBot\Services\DiscordInteractionService;
use Nwilging\LaravelDiscordBot\Support\Component;
use Nwilging\LaravelDiscordBot\Support\Components\ButtonComponent;
use Nwilging\LaravelDiscordBot\Support\Components\GenericButtonComponent;
use Nwilging\LaravelDiscordBot\Support\Interactions\Handlers\MessageComponentInteractionHandler;
use Nwilging\LaravelDiscordBot\Support\Interactions\InteractionHandler;
use Nwilging\LaravelDiscordBot\Support\Objects\EmojiObject;
use Nwilging\LaravelDiscordBotTests\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

class ButtonComponentTest extends TestCase
{
    public function testComponent()
    {
        $label = 'test label';

        $component = $this->getMockBuilder(ButtonComponent::class)->onlyMethods(['onClicked'])->setConstructorArgs([$label])->getMock();

        $this->assertArraySubset([
            'type' => Component::TYPE_BUTTON,
            'style' => GenericButtonComponent::STYLE_PRIMARY,
            'label' => $label,
        ], $component->toArray());
    }

    public function testComponentWithOptions()
    {
        $label = 'test label';

        $expectedEmojiArray = ['key' => 'value'];

        $emoji = \Mockery::mock(EmojiObject::class);
        $emoji->shouldReceive('toArray')->andReturn($expectedEmojiArray);

        $component = $this->getMockBuilder(ButtonComponent::class)->onlyMethods(['onClicked'])->setConstructorArgs([$label])->getMock();
        $component->withEmoji($emoji);
        $component->disabled();

        $this->assertArraySubset([
            'type' => Component::TYPE_BUTTON,
            'style' => GenericButtonComponent::STYLE_PRIMARY,
            'label' => $label,
            'disabled' => true,
            'emoji' => $expectedEmojiArray,
        ], $component->toArray());
    }

    /**
     * @dataProvider withStyleDataProvider
     */
    public function testComponentWithStyle(int $expectedStyle)
    {
        $label = 'test label';

        $component = $this->getMockBuilder(ButtonComponent::class)->onlyMethods(['onClicked'])->setConstructorArgs([$label])->getMock();

        switch ($expectedStyle) {
            case GenericButtonComponent::STYLE_PRIMARY:
                $component->withPrimaryStyle();
                break;
            case GenericButtonComponent::STYLE_SECONDARY:
                $component->withSecondaryStyle();
                break;
            case GenericButtonComponent::STYLE_SUCCESS:
                $component->withSuccessStyle();
                break;
            case GenericButtonComponent::STYLE_DANGER:
                $component->withDangerStyle();
                break;
        }

        $this->assertArraySubset([
            'type' => Component::TYPE_BUTTON,
            'style' => $expectedStyle,
            'label' => $label,
        ], $component->toArray());
    }

    public function testComponentInteraction()
    {
        $label = 'test label';
        $interactionRequest = new ParameterBag(['id' => '1']);

        $component = $this->getMockBuilder(ButtonComponent::class)->onlyMethods(['onClicked'])->setConstructorArgs([$label])->getMock();
        $component->expects($this->once())
            ->method('onClicked')
            ->with($interactionRequest);
        $job = new DiscordInteractionHandlerJob($interactionRequest, $component);
        $job->handle();
    }

    public function withStyleDataProvider(): array
    {
        return [
            [GenericButtonComponent::STYLE_PRIMARY],
            [GenericButtonComponent::STYLE_SECONDARY],
            [GenericButtonComponent::STYLE_SUCCESS],
            [GenericButtonComponent::STYLE_DANGER],
        ];
    }
}
