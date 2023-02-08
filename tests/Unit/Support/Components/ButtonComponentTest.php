<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Components;

use Illuminate\Http\Request;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Jobs\DiscordInteractionHandlerJob;
use Nwilging\LaravelDiscordBot\Support\Components\ButtonComponent;
use Nwilging\LaravelDiscordBot\Support\Components\GenericButtonInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Endpoints\ButtonInteractionEndpoint;
use Nwilging\LaravelDiscordBot\Support\Objects\EmojiObject;
use Nwilging\LaravelDiscordBotTests\TestCase;

class ButtonComponentTest extends TestCase
{
    public function testComponent()
    {
        $label = 'test label';

        $component = new ButtonComponent($label);

        $this->assertArraySubset([
            'type' => DiscordComponent::TYPE_BUTTON,
            'style' => GenericButtonInteractableComponent::STYLE_PRIMARY,
            'label' => $label,
        ], $component->toArray());
    }

    public function testComponentWithOptions()
    {
        $label = 'test label';

        $expectedEmojiArray = ['key' => 'value'];

        $emoji = \Mockery::mock(EmojiObject::class);
        $emoji->shouldReceive('toArray')->andReturn($expectedEmojiArray);

        $component = new ButtonComponent($label);
        $component->withEmoji($emoji);
        $component->disabled();

        $this->assertArraySubset([
            'type' => DiscordComponent::TYPE_BUTTON,
            'style' => GenericButtonInteractableComponent::STYLE_PRIMARY,
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

        $component = new ButtonComponent($label);

        switch ($expectedStyle) {
            case GenericButtonInteractableComponent::STYLE_PRIMARY:
                $component->withPrimaryStyle();
                break;
            case GenericButtonInteractableComponent::STYLE_SECONDARY:
                $component->withSecondaryStyle();
                break;
            case GenericButtonInteractableComponent::STYLE_SUCCESS:
                $component->withSuccessStyle();
                break;
            case GenericButtonInteractableComponent::STYLE_DANGER:
                $component->withDangerStyle();
                break;
        }

        $this->assertArraySubset([
            'type' => DiscordComponent::TYPE_BUTTON,
            'style' => $expectedStyle,
            'label' => $label,
        ], $component->toArray());
    }

    public function testComponentInteraction()
    {
        $label = 'test label';
        $interactionRequest = ['data' => ['id' => '1']];

        $endpoint = $this->getMockBuilder(ButtonInteractionEndpoint::class)->onlyMethods(['onClick'])->setConstructorArgs([$label])->getMock();
        $endpoint->expects($this->once())
            ->method('onClick')
            ->with($interactionRequest);
        $job = new DiscordInteractionHandlerJob($interactionRequest, $endpoint);
        $job->handle();
    }

    public function withStyleDataProvider(): array
    {
        return [
            [GenericButtonInteractableComponent::STYLE_PRIMARY],
            [GenericButtonInteractableComponent::STYLE_SECONDARY],
            [GenericButtonInteractableComponent::STYLE_SUCCESS],
            [GenericButtonInteractableComponent::STYLE_DANGER],
        ];
    }
}
