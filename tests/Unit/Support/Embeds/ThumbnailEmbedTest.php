<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Embeds\ThumbnailEmbed;
use Nwilging\LaravelDiscordBotTests\TestCase;

class ThumbnailEmbedTest extends TestCase
{
    public function testEmbed()
    {
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';
        $embed = new Embed($title, $description, $timestamp);
        $url = 'https://example.com';

        $embed->withThumbnail(new ThumbnailEmbed($url));

        $this->assertEquals([
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
            'thumbnail' => [
                'url' => $url,
            ],
        ], $embed->toArray());
    }

    public function testEmbedWithOptions()
    {
        $url = 'https://example.com';
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';

        $proxyUrl = 'https://example.com/proxy';
        $height = 256;
        $width = 512;
        $embed = new Embed($title, $description, $timestamp);

        $thumbNailEmbed = new ThumbnailEmbed($url, $title);

        $thumbNailEmbed->withProxyUrl($proxyUrl);
        $thumbNailEmbed->withWidth($width);
        $thumbNailEmbed->withHeight($height);

        $embed->withThumbnail($thumbNailEmbed);

        $this->assertEquals([
            'thumbnail' => [
                'url' => $url,
                'proxy_url' => $proxyUrl,
                'height' => $height,
                'width' => $width,
            ],
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
        ], $embed->toArray());
    }
}
