<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Embeds\ProviderEmbed;
use Nwilging\LaravelDiscordBotTests\TestCase;

class ProviderEmbedTest extends TestCase
{
    public function testEmbed()
    {
        $name = 'test name';
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';
        $url = 'url.com';
        $embed = new Embed($title, $description, $url, $timestamp);

        $providerEmbed = new ProviderEmbed($name);
        $embed->withProvider($providerEmbed);

        $this->assertEquals([
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
            'url' => $url,
            'provider' => [
                'name' => $name,
            ],
        ], $embed->toArray());
    }

    public function testEmbedWithOptions()
    {
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';
        $url = 'https://example.com';
        $name = 'test name';
        $embedUrl = 'url.com';

        $embed = new Embed($title, $description, $embedUrl, $timestamp);

        $providerEmbed = new ProviderEmbed($name, $url);
        $embed->withProvider($providerEmbed);

        $this->assertEquals([
            'provider' => [
                'url' => $url,
                'name' => $name,
            ],
            'url' => $embedUrl,
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
        ], $embed->toArray());
    }
}
