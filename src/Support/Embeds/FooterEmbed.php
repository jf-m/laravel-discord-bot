<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;

/**
 * Footer Embed
 * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-footer-structure
 */
class FooterEmbed extends EmbedObject
{
    protected string $text;

    protected ?string $iconUrl = null;

    protected ?string $proxyIconUrl = null;

    /**
     * @param string $text
     * @param string|null $iconUrl
     * @param string|null $proxyIconUrl
     */
    public function __construct(string $text, ?string $iconUrl = null, ?string $proxyIconUrl = null)
    {
        $this->text = $text;
        $this->iconUrl = $iconUrl;
        $this->proxyIconUrl = $proxyIconUrl;
    }


    /**
     * URL of footer icon (only supports http(s) and attachments)
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-footer-structure
     * @param string $iconUrl
     * @return $this
     */
    public function withIconUrl(string $iconUrl): self
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }

    /**
     * A proxied url of footer icon
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-footer-structure
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
            'text' => $this->text,
            'icon_url' => $this->iconUrl,
            'proxy_icon_url' => $this->proxyIconUrl,
        ]);
    }
}
