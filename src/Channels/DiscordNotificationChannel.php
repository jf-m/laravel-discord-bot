<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Channels;

use Nwilging\LaravelDiscordBot\Contracts\Channels\DiscordNotificationChannelContract;
use Nwilging\LaravelDiscordBot\Contracts\Notifications\DiscordNotificationContract;
use Nwilging\LaravelDiscordBot\Contracts\Services\DiscordApiServiceContract;
use Nwilging\LaravelDiscordBot\Messages\PlainDiscordMessage;
use Nwilging\LaravelDiscordBot\Messages\RichDiscordMessage;

class DiscordNotificationChannel implements DiscordNotificationChannelContract
{
    protected DiscordApiServiceContract $discordApiService;

    public function __construct(DiscordApiServiceContract $discordApiService)
    {
        $this->discordApiService = $discordApiService;
    }

    public function send($notifiable, DiscordNotificationContract $notification): array
    {
        $notificationMessage = $notification->toDiscord($notifiable);
        return $this->discordApiService->sendMessage($notificationMessage->channelId, $notificationMessage->message, $notificationMessage->embeds, $notificationMessage->components, $notificationMessage->options);
    }
}
