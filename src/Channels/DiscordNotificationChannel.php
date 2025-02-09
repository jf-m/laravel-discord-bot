<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Channels;

use Nwilging\LaravelDiscordBot\Contracts\Channels\DiscordNotificationChannelContract;
use Nwilging\LaravelDiscordBot\Contracts\Notifications\DiscordNotificationContract;
use Nwilging\LaravelDiscordBot\Facades\Discord;

class DiscordNotificationChannel implements DiscordNotificationChannelContract
{

    public function send($notifiable, DiscordNotificationContract $notification): array
    {
        return Discord::sendMessage($notification->toDiscord($notifiable));
    }
}
