<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Builder;

use Nwilging\LaravelDiscordBot\Support\Builder\EmbedBuilder;
use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Embeds\EmbedObject;
use Nwilging\LaravelDiscordBotTests\TestCase;

class EmbedBuilderTest extends TestCase
{
    public function testBuilderBasics()
    {
        $builder = new EmbedBuilder();

        $expectedEmbed1Array = ['k1' => 'v1'];
        $expectedEmbed2Array = ['k2' => 'v2'];

        $embed1 = \Mockery::mock(EmbedObject::class);
        $embed2 = \Mockery::mock(EmbedObject::class);

        $embed1->shouldReceive('toArray')->andReturn($expectedEmbed1Array);
        $embed2->shouldReceive('toArray')->andReturn($expectedEmbed2Array);

        $builder->addEmbed($embed1)->addEmbed($embed2);

        $this->assertSame([$embed1, $embed2], $builder->getEmbeds());
        $this->assertEquals([
            $expectedEmbed1Array,
            $expectedEmbed2Array,
        ], $builder->toArray());
    }

    public function testAddFooter()
    {
        $footerText = 'test text';

        $builder = new EmbedBuilder();
        $builder->addFooter($footerText);

        $this->assertEquals([
            [
                'text' => $footerText,
            ]
        ], $builder->toArray());
    }

    public function testAddImage()
    {
        $imageUrl = 'test url';

        $builder = new EmbedBuilder();
        $builder->addImage($imageUrl);

        $this->assertEquals([
            [
                'url' => $imageUrl,
            ]
        ], $builder->toArray());
    }

    public function testAddThumbnail()
    {
        $imageUrl = 'test url';

        $builder = new EmbedBuilder();
        $builder->addThumbnail($imageUrl);

        $this->assertEquals([
            [
                'url' => $imageUrl,
            ]
        ], $builder->toArray());
    }

    public function testAddVideo()
    {
        $videoUrl = 'test url';

        $builder = new EmbedBuilder();
        $builder->addVideo($videoUrl);

        $this->assertEquals([
            [
                'url' => $videoUrl,
            ]
        ], $builder->toArray());
    }

    public function testAddProvider()
    {
        $name = 'test name';
        $url = 'test url';

        $builder = new EmbedBuilder();
        $builder->addProvider($name, $url);

        $this->assertEquals([
            [
                'name' => $name,
                'url' => $url,
            ]
        ], $builder->toArray());
    }

    public function testAddAuthor()
    {
        $authorName = 'test name';

        $builder = new EmbedBuilder();
        $builder->addAuthor($authorName);

        $this->assertEquals([
            [
                'name' => $authorName,
            ]
        ], $builder->toArray());
    }
}
