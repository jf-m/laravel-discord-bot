<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Embeds\VideoEmbed;
use Nwilging\LaravelDiscordBotTests\TestCase;

class VideoEmbedTest extends TestCase
{
    public function testEmbed()
    {
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';
        $url = 'https://example.com';
        $embed = new Embed($title, $description, $timestamp);

        $embed->withVideo(new VideoEmbed($url));

        $this->assertEquals([
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
            'video' => [
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


        $videoEmbed = new VideoEmbed($url, $title);

        $videoEmbed->withProxyUrl($proxyUrl);
        $videoEmbed->withWidth($width);
        $videoEmbed->withHeight($height);
        $embed->withVideo($videoEmbed);

        $this->assertEquals([
            'video' => [
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
