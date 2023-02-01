<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Components;

use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordComponent;

class ActionRow implements DiscordComponent
{
    protected ?string $content;

    /**
     * @var DiscordComponent[]
     */
    protected array $components = [];

    /**
     * @param DiscordComponent[] $components
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

    public function addComponent(DiscordComponent $component): static
    {
        $this->components[] = $component;
        return $this;
    }

    public function getType(): int
    {
        return DiscordComponent::TYPE_ACTION_ROW;
    }

    public function validate(): void
    {
        foreach ($this->components as $component) {
            $component->validate();
        }
    }

    public function toArray(): array
    {
        return array_filter([
            'content' => $this->content,
            'type' => $this->getType(),
            'components' => array_map(function (DiscordComponent $component): array {
                return $component->toArray();
            }, $this->components),
        ]);
    }
}
