<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;

use Illuminate\Contracts\Support\Arrayable;
use Nwilging\LaravelDiscordBot\Support\Component;
use Nwilging\LaravelDiscordBot\Support\InteractableComponent;

class ActionRow extends Component
{
    protected ?string $content;

    /**
     * @var Component[]
     */
    protected array $components = [];

    /**
     * @param Component[] $components
     * @param ?string $content
     */
    public function __construct(array $components = [], ?string $content = null)
    {
        $this->components = $components;
        $this->content = $content;
    }

    public function content(?string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function addComponent(Component $component): static
    {
        $this->components[] = $component;
        return $this;
    }

    protected function getType()
    {
        return Component::TYPE_ACTION_ROW;
    }

    public function toArray(): array
    {
        return array_filter([
            'content' => $this->content,
            'type' => $this->getType(),
            'components' => array_map(function (Component $component): array {
                return $component->toArray();
            }, $this->components),
        ]);
    }
}
