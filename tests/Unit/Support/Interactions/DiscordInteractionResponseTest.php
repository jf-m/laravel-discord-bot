<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Support\Interactions;

use Nwilging\LaravelDiscordBot\Support\Interactions\DiscordInteractionResponse;
use Nwilging\LaravelDiscordBotTests\TestCase;

class DiscordInteractionResponseTest extends TestCase
{
    public function testClass()
    {
        $code = 201;
        $type = 12;

        $response = new DiscordInteractionResponse($type, $code);

        $this->assertSame($code, $response->getStatus());
        $this->assertEquals([
            'type' => $type
        ], $response->toArray());
    }
}
