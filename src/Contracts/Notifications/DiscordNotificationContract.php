<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Contracts\Notifications;

use Nwilging\LaravelDiscordBot\Messages\DiscordMessage;

interface DiscordNotificationContract
{
    /**
     * Returns a Discord-API compliant notification array.
     *
     * @param mixed $notifiable
     * @return DiscordMessage
     */
    public function toDiscord($notifiable): DiscordMessage;
}
