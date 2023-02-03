<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests\Unit\Channels;

use Illuminate\Notifications\Notifiable;
use Nwilging\LaravelDiscordBot\Channels\DiscordNotificationChannel;
use Nwilging\LaravelDiscordBot\Contracts\Notifications\DiscordNotificationContract;
use Nwilging\LaravelDiscordBot\Contracts\Support\DiscordInteractableComponent;
use Nwilging\LaravelDiscordBot\Facades\Discord;
use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;
use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBotTests\TestCase;

class DiscordNotificationChannelTest extends TestCase
{
    public function testSendPlainText()
    {
        $notifiable = \Mockery::mock(Notifiable::class);
        $notification = \Mockery::mock(DiscordNotificationContract::class);

        $discordNotificationMessage = (new DiscordMessage())
            ->channelId('12345')
            ->message('test message');

        $expectedResponse = [
            'key' => 'value',
        ];

        $notification->shouldReceive('toDiscord')
            ->once()
            ->with($notifiable)
            ->andReturn($discordNotificationMessage);

        Discord::shouldReceive('sendMessage')
            ->once()
            ->with($discordNotificationMessage)
            ->andReturn($expectedResponse);

        $result = (new DiscordNotificationChannel())->send($notifiable, $notification);
        $this->assertEquals($expectedResponse, $result);
    }

    public function testSendRichText()
    {
        $notifiable = \Mockery::mock(Notifiable::class);
        $notification = \Mockery::mock(DiscordNotificationContract::class);

        $embed1 = \Mockery::mock(Embed::class);
        $embed2 = \Mockery::mock(Embed::class);

        $component1 = \Mockery::mock(DiscordInteractableComponent::class);
        $component2 = \Mockery::mock(DiscordInteractableComponent::class);

        $discordNotificationMessage = (new DiscordMessage())
            ->channelId('12345')
            ->embeds([$embed1, $embed2])
            ->components([$component1, $component2]);

        $expectedResponse = [
            'key' => 'value',
        ];

        $notification->shouldReceive('toDiscord')
            ->once()
            ->with($notifiable)
            ->andReturn($discordNotificationMessage);

        Discord::shouldReceive('sendMessage')
            ->once()
            ->with($discordNotificationMessage)
            ->andReturn($expectedResponse);

        $result = (new DiscordNotificationChannel())->send($notifiable, $notification);
        $this->assertEquals($expectedResponse, $result);
    }
}
