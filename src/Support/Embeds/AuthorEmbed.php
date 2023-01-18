<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;

/**
 * Author Embed
 * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-author-structure
 */
class AuthorEmbed extends EmbedObject
{
    protected string $name;

    protected ?string $url = null;

    protected ?string $iconUrl = null;

    protected ?string $proxyIconUrl = null;

    /**
     * @param string $name
     * @param string|null $url
     * @param string|null $iconUrl
     * @param string|null $proxyIconUrl
     */
    public function __construct(string $name, ?string $url = null, ?string $iconUrl = null, ?string $proxyIconUrl = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->iconUrl = $iconUrl;
        $this->proxyIconUrl = $proxyIconUrl;
    }


    /**
     * URL of author
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-author-structure
     * @param string $url
     * @return $this
     */
    public function withUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * URL of author icon (only supports http(s) and attachments)
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-author-structure
     * @param string $iconUrl
     * @return $this
     */
    public function withIconUrl(string $iconUrl): self
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }

    /**
     * A proxied url of author icon
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-author-structure
     * @param string $proxyIconUrl
     * @return $this
     */
    public function withProxyIconUrl(string $proxyIconUrl): self
    {
        $this->proxyIconUrl = $proxyIconUrl;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'url' => $this->url,
            'icon_url' => $this->iconUrl,
            'proxy_icon_url' => $this->proxyIconUrl,
        ]);
    }
}
