<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Components;

use Nwilging\LaravelDiscordBot\Jobs\DiscordInteractionHandlerJob;
use Nwilging\LaravelDiscordBot\Support\Component;
use Nwilging\LaravelDiscordBot\Support\InteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Components\GenericTextInputInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Components\ParagraphTextInputComponent;
use Nwilging\LaravelDiscordBotTests\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

class ParagraphTextInputComponentTest extends TestCase
{
    public function testComponent()
    {
        $label = 'label';

        $component = $this->getMockBuilder(ParagraphTextInputComponent::class)->onlyMethods(['onTextSubmitted'])->setConstructorArgs([$label])->getMock();

        $this->assertArraySubset([
            'type' => Component::TYPE_TEXT_INPUT,
            'style' => GenericTextInputInteractableComponent::STYLE_PARAGRAPH,
            'label' => $label,
        ], $component->toArray());
    }

    public function testComponentWithOptions()
    {
        $label = 'label';

        $minLength = 5;
        $maxLength = 10;
        $placeholder = 'test placeholder';
        $value = 'test value';

        $component = $this->getMockBuilder(ParagraphTextInputComponent::class)->onlyMethods(['onTextSubmitted'])->setConstructorArgs([$label])->getMock();

        $component->withPlaceholder($placeholder);
        $component->withMinLength($minLength);
        $component->withMaxLength($maxLength);
        $component->withValue($value);
        $component->required();

        $this->assertArraySubset([
            'type' => Component::TYPE_TEXT_INPUT,
            'style' => GenericTextInputInteractableComponent::STYLE_PARAGRAPH,
            'label' => $label,
            'min_length' => $minLength,
            'max_length' => $maxLength,
            'placeholder' => $placeholder,
            'value' => $value,
            'required' => true,
        ], $component->toArray());
    }

    public function testComponentInteraction()
    {
        $label = 'test label';
        $value = 'test';
        $interactionRequest = new ParameterBag(['value' => $value, 'id' => '1']);

        $component = $this->getMockBuilder(ParagraphTextInputComponent::class)->onlyMethods(['onTextSubmitted'])->setConstructorArgs([$label, $value])->getMock();
        $component->expects($this->once())
            ->method('onTextSubmitted')
            ->with($value, $interactionRequest);
        $job = new DiscordInteractionHandlerJob($interactionRequest, $component);
        $job->handle();
    }
}
