<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Embeds\AuthorEmbed;
use Nwilging\LaravelDiscordBotTests\TestCase;

class AuthorEmbedTest extends TestCase
{
    public function testEmbed()
    {
        $name = 'test name';
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';
        $url = 'url.com';
        $embed = new Embed($title, $description, $url, $timestamp);

        $embed->withAuthor(new AuthorEmbed($name));

        $this->assertEquals([
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
            'author' => [
                'name' => $name,
            ],
        ], $embed->toArray());
    }

    public function testEmbedWithOptions()
    {
        $name = 'test name';
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';

        $url = 'https://example.com';
        $iconUrl = 'https://example.com/icon';
        $proxyIconUrl = 'https://example.com/proxy';
        $urlEmbed = 'url.com';
        $embed = new Embed($title, $description, $urlEmbed, $timestamp);

        $authorEmbed = new AuthorEmbed($name);

        $authorEmbed->withUrl($url);
        $authorEmbed->withIconUrl($iconUrl);
        $authorEmbed->withProxyIconUrl($proxyIconUrl);

        $embed->withAuthor($authorEmbed);

        $this->assertEquals([
            'author' => [
                'name' => $name,
                'url' => $url,
                'icon_url' => $iconUrl,
                'proxy_icon_url' => $proxyIconUrl,
            ],
            'url' => $urlEmbed,
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp
        ], $embed->toArray());
    }
}
