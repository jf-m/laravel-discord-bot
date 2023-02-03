<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Providers;

use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Nwilging\LaravelDiscordBot\Channels\DiscordNotificationChannel;
use Nwilging\LaravelDiscordBot\Contracts\Channels\DiscordNotificationChannelContract;
use Nwilging\LaravelDiscordBot\Contracts\Services\DiscordInteractionServiceContract;
use Nwilging\LaravelDiscordBot\Services\DiscordApiService;
use Nwilging\LaravelDiscordBot\Services\DiscordInteractionService;
use Nwilging\LaravelDiscordBot\Support\Interactions\Handlers\MessageComponentInteractionHandler;

class DiscordBotServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/discord.php' => config_path('discord.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/discord.php', 'discord');

        $this->app->bind('laravel-discord-bot', function($app) {
            /** @var Config $config */
            $config = $app->make(Config::class);
            return new DiscordApiService(
                $config->get('discord.token'),
                $config->get('discord.application_id'),
                $config->get('discord.api_url'),
                $app->make(Client::class)
            );
        });

        Notification::resolved(function (ChannelManager $channelManager): void {
            $channelManager->extend('discord', function (): DiscordNotificationChannelContract {
                return $this->app->make(DiscordNotificationChannelContract::class);
            });
        });

        $this->app->bind(DiscordInteractionServiceContract::class, DiscordInteractionService::class);
        $this->app->when(DiscordInteractionService::class)->needs('$applicationId')->give(function (): string {
            return $this->app->make(Config::class)->get('discord.application_id');
        });

        $this->app->when(DiscordInteractionService::class)->needs('$publicKey')->give(function (): string {
            return $this->app->make(Config::class)->get('discord.public_key');
        });

        $this->app->when(MessageComponentInteractionHandler::class)->needs('$defaultBehavior')->give(function (): string {
            return $this->app->make(Config::class)->get('discord.interactions.component_interaction_default_behavior');
        });

        $this->app->bind(DiscordNotificationChannelContract::class, DiscordNotificationChannel::class);
    }
}
