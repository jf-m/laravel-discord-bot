<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;

/**
 * Field Embed
 * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-field-structure
 */
class FieldEmbed extends EmbedObject
{
    protected string $name;

    protected string $value;

    protected ?bool $inline = null;

    /**
     * @param string $name
     * @param string $value
     * @param bool|null $inline
     */
    public function __construct(string $name, string $value, ?bool $inline = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->inline = $inline;
    }


    /**
     * Whether this field should display inline
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-field-structure
     * @param bool $inline
     * @return $this
     */
    public function inline(bool $inline = true): self
    {
        $this->inline = $inline;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'value' => $this->value,
            'inline' => $this->inline,
        ], fn($v) => $v !== null);
    }
}
