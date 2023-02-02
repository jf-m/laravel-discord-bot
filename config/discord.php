<?php

return [
    /**
     * API token generated for the Discord Bot
     */

    'token' => env('DISCORD_API_BOT_TOKEN'),


    /**
     * Discord API URL
     */

    'api_url' => env('DISCORD_API_URL', 'https://discord.com/api'),


    /**
     * Discord Application Id
     */

    'application_id' => env('DISCORD_APPLICATION_ID'),


    /**
     * Discord Public Key
     */
    'public_key' => env('DISCORD_PUBLIC_KEY'),


    /**
     * Limit of character for the custom_id parameters.
     * https://discord.com/developers/docs/interactions/message-components#custom-id
     */
    'custom_id_character_limit' => 100,


    /**
     * Discord Interaction related parameters
     */
    'interactions' => [
        /**
         * Namespaces of your custom components and modals
         * This is used to reduce the length of the custom_id parameter
         */
        'namespaces' => [
            "App\\",
            "Nwilging\\LaravelDiscordBot\\Support\\Components\\"
        ],
        /**
         * Default queue for the discord interactions
         */
        'default_queue' => null,
        /**
         * Default connection for the discord interactions
         */
        'default_connection' => 'redis',
        /**
         * When handling interactions the default response behavior is to "defer" - aka stop the loading process in the Discord
         * app window and return no reply or other message.
         */
        'component_interaction_default_behavior' => 'defer'
    ],
];
