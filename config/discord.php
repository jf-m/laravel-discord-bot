<?php
declare(strict_types=1);

$allowedDefaultBehaviorTypes = [
    'load',
    'defer',
];

return [
    'token' => env('DISCORD_API_BOT_TOKEN'),
    'api_url' => env('DISCORD_API_URL', 'https://discord.com/api'),
    'application_id' => env('DISCORD_APPLICATION_ID'),
    'public_key' => env('DISCORD_PUBLIC_KEY'),
    'custom_id_character_limit' => 100,
    'interactions' => [
        'namespaces' => [
            "App\\",
            "Nwilging\\LaravelDiscordBot\\Support\\Components\\"
        ],
        'default_queue' => null,
        'default_connection' => null,
        'component_interaction_default_behavior' => (in_array(
            env('DISCORD_COMPONENT_INTERACTION_DEFAULT_BEHAVIOR'),
            $allowedDefaultBehaviorTypes
        )) ? env('DISCORD_COMPONENT_INTERACTION_DEFAULT_BEHAVIOR') : 'defer',
    ],
];
