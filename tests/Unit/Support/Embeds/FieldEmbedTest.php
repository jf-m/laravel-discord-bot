<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Embeds\FieldEmbed;
use Nwilging\LaravelDiscordBotTests\TestCase;

class FieldEmbedTest extends TestCase
{
    public function testEmbed()
    {
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';
        $embed = new Embed($title, $description, $timestamp);
        $name = 'test name';
        $value = 'test value';
        $embed->withField(new FieldEmbed($name, $value));
        $this->assertEquals([
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
            'fields' => [
                [
                    'name' => $name,
                    'value' => $value,
                ]
            ],
        ], $embed->toArray());
    }

    public function testEmbedWithOptions()
    {
        $name = 'test name';
        $value = 'test value';
        $color = 12345;
        $title = 'test title';
        $description = 'test description';
        $timestamp = '12345';

        $embed = new Embed($title, $description, $timestamp);
        $embed->withColor($color);

        $fieldEmbed = new FieldEmbed($name, $value);
        $fieldEmbed->inline();

        $embed->withField($fieldEmbed);

        $this->assertEquals([
            'fields' => [
                [
                    'name' => $name,
                    'value' => $value,
                    'inline' => true,
                ]
            ],
            'title' => $title,
            'description' => $description,
            'timestamp' => $timestamp,
            'color' => $color,
        ], $embed->toArray());
    }
}
