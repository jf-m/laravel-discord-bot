<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;

/**
 * Image Embed
 * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-image-structure
 */
class ImageEmbed extends EmbedObject
{
    use MergesArrays;

    protected string $url;

    protected ?string $proxyUrl = null;

    protected ?int $height = null;

    protected ?int $width = null;

    /**
     * @param string $url
     * @param string|null $proxyUrl
     * @param int|null $height
     * @param int|null $width
     */
    public function __construct(string $url, ?string $proxyUrl = null, ?int $height = null, ?int $width = null)
    {
        $this->url = $url;
        $this->proxyUrl = $proxyUrl;
        $this->height = $height;
        $this->width = $width;
    }


    /**
     * A proxied url of the image
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-image-structure
     * @param string $proxyUrl
     * @return $this
     */
    public function withProxyUrl(string $proxyUrl): self
    {
        $this->proxyUrl = $proxyUrl;
        return $this;
    }

    /**
     * Image height
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-image-structure
     * @param int $height
     * @return $this
     */
    public function withHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Image width
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-image-structure
     * @param int $width
     * @return $this
     */
    public function withWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'url' => $this->url,
            'proxy_url' => $this->proxyUrl,
            'height' => $this->height,
            'width' => $this->width,
        ]);
    }
}
