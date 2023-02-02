<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Embeds\ImageEmbed;
use Nwilging\LaravelDiscordBotTests\TestCase;

class ImageEmbedTest extends TestCase
{
    public function testEmbed()
    {
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';
        $url = 'https://example.com';
        $embedUrl = 'url.com';
        $embed = new Embed($title, $description, $embedUrl, $timestamp);
        $embed->withImage(new ImageEmbed($url));

        $this->assertEquals([
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
            'url' => $embedUrl,
            'image' => [
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
        $embedUrl = 'url.com';
        $height = 256;
        $width = 512;

        $embed = new Embed($title, $description, $embedUrl, $timestamp);

        $imageEmbed = new ImageEmbed($url);

        $imageEmbed->withProxyUrl($proxyUrl);
        $imageEmbed->withWidth($width);
        $imageEmbed->withHeight($height);

        $embed->withImage($imageEmbed);

        $this->assertEquals([
            'image' => [
                'url' => $url,
                'proxy_url' => $proxyUrl,
                'height' => $height,
                'width' => $width,
            ],
            'url' => $embedUrl,
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
        ], $embed->toArray());
    }
}
