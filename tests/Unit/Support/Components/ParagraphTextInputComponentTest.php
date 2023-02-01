<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Components;

use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;
use Nwilging\LaravelDiscordBot\Jobs\DiscordInteractionHandlerJob;
use Nwilging\LaravelDiscordBot\Support\Components\GenericTextInputInteractableComponent;
use Nwilging\LaravelDiscordBot\Support\Components\ParagraphTextInputComponent;
use Nwilging\LaravelDiscordBotTests\TestCase;

class ParagraphTextInputComponentTest extends TestCase
{
    public function testComponent()
    {
        $label = 'label';

        $component = $this->getMockBuilder(ParagraphTextInputComponent::class)->onlyMethods([])->setConstructorArgs([$label])->getMock();

        $this->assertArraySubset([
            'type' => DiscordComponent::TYPE_TEXT_INPUT,
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

        $component = $this->getMockBuilder(ParagraphTextInputComponent::class)->onlyMethods([])->setConstructorArgs([$label])->getMock();

        $component->withPlaceholder($placeholder);
        $component->withMinLength($minLength);
        $component->withMaxLength($maxLength);
        $component->withValue($value);
        $component->required();

        $this->assertArraySubset([
            'type' => DiscordComponent::TYPE_TEXT_INPUT,
            'style' => GenericTextInputInteractableComponent::STYLE_PARAGRAPH,
            'label' => $label,
            'min_length' => $minLength,
            'max_length' => $maxLength,
            'placeholder' => $placeholder,
            'value' => $value,
            'required' => true,
        ], $component->toArray());
    }
}
