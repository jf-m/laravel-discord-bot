<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support;

use Illuminate\Contracts\Support\Arrayable;
use Nwilging\LaravelDiscordBot\Support\Embeds\AuthorEmbed;
use Nwilging\LaravelDiscordBot\Support\Embeds\FieldEmbed;
use Nwilging\LaravelDiscordBot\Support\Embeds\FooterEmbed;
use Nwilging\LaravelDiscordBot\Support\Embeds\ImageEmbed;
use Nwilging\LaravelDiscordBot\Support\Embeds\ProviderEmbed;
use Nwilging\LaravelDiscordBot\Support\Embeds\ThumbnailEmbed;
use Nwilging\LaravelDiscordBot\Support\Embeds\VideoEmbed;
use Nwilging\LaravelDiscordBot\Support\Traits\FiltersRecursive;

/**
 * Embed Object
 * @see https://discord.com/developers/docs/resources/channel#embed-object
 */
class Embed implements Arrayable
{
    protected ?string $url = null;

    protected ?string $title = null;

    protected ?string $description = null;

    protected ?string $timestamp = null;

    protected ?int $color = null;

    /**
     * @var array<FieldEmbed>|null
     */
    protected ?array $fields = null;

    protected ?VideoEmbed $video = null;

    protected ?AuthorEmbed $author = null;

    protected ?FooterEmbed $footer = null;

    protected ?ImageEmbed $image = null;

    protected ?ProviderEmbed $provider = null;

    protected ?ThumbnailEmbed $thumbnail = null;

    public function __construct(?string $title = null, ?string $description = null, ?string $url = null, ?string $timestamp = null)
    {
        $this->title = $title;
        $this->url = $url;
        $this->description = $description;
        $this->timestamp = $timestamp;
    }

    /**
     * The color code of the embed
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-structure
     *
     * @param int $colorCode
     * @return $this
     */
    public function withColor(int $colorCode): self
    {
        $this->color = $colorCode;
        return $this;
    }

    public function withField(FieldEmbed $field): self
    {
        if ($this->fields === null) {
            $this->fields = [];
        }
        $this->fields[] = $field;
        return $this;
    }

    public function withVideo(VideoEmbed $video): self
    {
        $this->video = $video;
        return $this;
    }

    public function withURL(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function withAuthor(AuthorEmbed $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function withFooter(FooterEmbed $footer): self
    {
        $this->footer = $footer;
        return $this;
    }

    public function withImage(ImageEmbed $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function withProvider(ProviderEmbed $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    public function withThumbnail(ThumbnailEmbed $thumbnail): self
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * Returns a Discord-API compliant embed array
     *
     * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-structure
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'timestamp' => $this->timestamp,
            'color' => $this->color,
            'url' => $this->url,
            'fields' => array_map(fn(FieldEmbed $embed) => $embed->toArray(), $this->fields ?: []),
            'video' => $this->video?->toArray(),
            'author' => $this->author?->toArray(),
            'footer' => $this->footer?->toArray(),
            'image' => $this->image?->toArray(),
            'provider' => $this->provider?->toArray(),
            'thumbnail' => $this->thumbnail?->toArray()
        ]);
    }
}
