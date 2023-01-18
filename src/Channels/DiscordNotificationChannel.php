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
        if ($notificationMessage instanceof PlainDiscordMessage) {
            return $this->handleTextMessage($notificationMessage);
        } else if ($notificationMessage instanceof RichDiscordMessage) {
            return $this->handleRichTextMessage($notificationMessage);
        } else {
            throw new \InvalidArgumentException(sprintf('%s is not a valid DiscordMessage', get_class($notificationMessage)));

        }
    }

    protected function handleTextMessage(PlainDiscordMessage $notificationMessage): array
    {
        return $this->discordApiService->sendTextMessage($notificationMessage->channelId, $notificationMessage->message, $notificationMessage->options ?: []);
    }

    protected function handleRichTextMessage(RichDiscordMessage $notificationMessage): array
    {
        return $this->discordApiService->sendRichTextMessage($notificationMessage->channelId, $notificationMessage->embeds ?: [], $notificationMessage->components ?: [], $notificationMessage->options ?: []);
    }
}
