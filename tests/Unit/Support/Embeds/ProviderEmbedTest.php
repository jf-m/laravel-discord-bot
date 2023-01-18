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
        $embed = new Embed($title, $description, $timestamp);

        $providerEmbed = new ProviderEmbed($name);
        $embed->withProvider($providerEmbed);

        $this->assertEquals([
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
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

        $embed = new Embed($title, $description, $timestamp);

        $providerEmbed = new ProviderEmbed($name, $url);
        $embed->withProvider($providerEmbed);

        $this->assertEquals([
            'provider' => [
                'url' => $url,
                'name' => $name,
            ],
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
        ], $embed->toArray());
    }
}
