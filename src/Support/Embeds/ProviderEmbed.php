<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;

/**
 * Provider Embed
 * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-provider-structure
 */
class ProviderEmbed extends EmbedObject
{
    use MergesArrays;

    protected ?string $name = null;

    protected ?string $url = null;

    /**
     * @param string $name
     * @param string|null $url
     */
    public function __construct(string $name, ?string $url = null)
    {
        $this->name = $name;
        $this->url = $url;
    }


    /**
     * Name of provider
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-provider-structure
     * @param string $name
     * @return $this
     */
    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * URL of provider
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-provider-structure
     * @param string $url
     * @return $this
     */
    public function withUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'url' => $this->url,
        ]);
    }
}
