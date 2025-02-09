<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Embeds\FooterEmbed;
use Nwilging\LaravelDiscordBotTests\TestCase;

class FooterEmbedTest extends TestCase
{
    public function testEmbed()
    {
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';
        $url = 'url.com';
        $embed = new Embed($title, $description, $url, $timestamp);


        $text = 'test text';
        $embed->withFooter(new FooterEmbed($text));


        $this->assertEquals([
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
            'footer' => [
                'text' => $text,
            ],
        ], $embed->toArray());
    }

    public function testEmbedWithOptions()
    {
        $text = 'test text';
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';
        $url = 'url.com';

        $iconUrl = 'https://example.com/proxy';
        $proxyIconUrl = 'https://example.com/proxy';
        $embed = new Embed($title, $description, $url, $timestamp);

        $footerEmbed = new FooterEmbed($text, $title, $description);

        $footerEmbed->withIconUrl($iconUrl);
        $footerEmbed->withProxyIconUrl($proxyIconUrl);

        $embed->withColor(12345);

        $embed->withFooter($footerEmbed);

        $this->assertEquals([
            'footer' => [
                'text' => $text,
                'icon_url' => $iconUrl,
                'proxy_icon_url' => $proxyIconUrl,
            ],
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
            'color' => 12345,
        ], $embed->toArray());
    }
}
