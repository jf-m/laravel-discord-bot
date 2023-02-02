# 

Laravel Discord Bot
A robust Discord messaging integration for Laravel

![Tests](https://github.com/nwilging/laravel-discord-bot/actions/workflows/main-branch.yml/badge.svg?branch=main)
![Coverage](./.github/coverage-badge.svg)
[![Latest Stable Version](http://poser.pugx.org/nwilging/laravel-discord-bot/v)](https://packagist.org/packages/nwilging/laravel-discord-bot)
[![License](http://poser.pugx.org/nwilging/laravel-discord-bot/license)](https://packagist.org/packages/nwilging/laravel-discord-bot)
[![Total Downloads](http://poser.pugx.org/nwilging/laravel-discord-bot/downloads)](https://packagist.org/packages/nwilging/laravel-discord-bot)

---
### About

This package provides a notification channel to send messages to Discord, as well as a suite of tools, services,
and components that will help you to build rich-text messages as well as handle
[Discord Interactions](https://discord.com/developers/docs/interactions/receiving-and-responding).

---

# Installation

### Pre Requisites
1. Laravel v9+
2. PHP 8.0+
3. [libsodium](https://doc.libsodium.org/bindings_for_other_languages#programming-languages-whose-standard-library-includes-support-for-libsodium)

### Install with Composer
```
composer require nwilging/laravel-discord-bot
```

### Configuration

1. Visit the [Discord Developer Portal](https://discord.com/developers/applications) and create a new Application
2. Copy the Application ID and the Public Key
3. Create a Bot User
4. Reset the Bot User Token and copy it

Populate your `.env`:
```
DISCORD_API_BOT_TOKEN=<bot user token from above>
DISCORD_APPLICATION_ID=<application id from above>
DISCORD_PUBLIC_KEY=<public key from above>
```

#### Additional Configuration (optional)

Execute the following command to publish the `discord.php` configuration file into your `config` folder:

```
php artisan vendor:publish --provider="JohnDoeNwilging\LaravelDiscordBot\Providers\DiscordBotServiceProvider" --tag="config"
```

`interactions.default_connection`

The default behavior is to use `redis` as the default connection for all the Interactions with Discord.

`interactions.default_queue`

The default behavios is to use your default queue of your connection type. You can change the default queue to a specific queue in your `discord.php` config file.

`interactions.component_interaction_default_behavior`

When handling interactions the default response behavior is to "defer" - aka stop the loading process in the Discord
app window and return no reply or other message. You can change it to "load" - which will show a loading
message until your application sends a followup message - in your `discord.php` config file.


# Notification Channel Usage

Your notification class must implement the interface `Nwilging\LaravelDiscordBot\Contracts\Notifications\DiscordNotificationContract`, and include the `toDiscord(): array` method.

```php
class TestNotification extends Notification implements DiscordNotificationContract
{
    use Queueable;

    public function via($notifiable)
    {
        return ['discord'];
    }

    public function toDiscord($notifiable): DiscordMessage
    {
        return (new DiscordMessage())
            ->channelId('discord channel ID')
            ->message('My message content');
    }
}
```

## Embeds

Each `DiscordMessage` can include multiple `Embeds`

```php
class TestNotification extends Notification implements DiscordNotificationContract
{
    use Queueable;

    public function via($notifiable)
    {
        return ['discord'];
    }

    public function toDiscord($notifiable): DiscordMessage
    {
        $embed = new Embed('Birman', 'The Birman, also called the "Sacred Cat of Burma", is a domestic cat breed.', '2022-10-21T00:00:00.000Z');
        $embed->withColor(16711680) // Red
              ->withImage(new ImageEmbed('https://upload.wikimedia.org/wikipedia/commons/0/06/Birmanstrofe.jpg'))
              ->withAuthor(new AuthorEmbed('Cat behaviorist', 'https://en.wikipedia.org/wiki/Cat_behaviorist'))
              ->withFooter(new FooterEmbed('Thanks for reading', 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/P%C3%A9pita_Sacr%C3%A9_de_Birmanie.jpg/1024px-P%C3%A9pita_Sacr%C3%A9_de_Birmanie.jpg'))
              ->withField((new FieldEmbed('Weight', '2.7 - 5.4kg'))->inline())
              ->withField((new FieldEmbed('Lifespan', '12 - 16 years'))->inline());

        return (new DiscordMessage())->channelId('MY_CHANNEL_ID')
                                     ->message("Want to know more about the Birman cats? Here!")
                                     ->embeds([$embed]);
    }
}
```

## Components

Each `DiscordMessage` can include multiple `LinkButtonComponent` and Interactable Components

```php
class TestNotification extends Notification implements DiscordNotificationContract
{
    use Queueable;

    public function via($notifiable)
    {
        return ['discord'];
    }

    public function toDiscord($notifiable): DiscordMessage
    {
        return (new DiscordMessage())->channelId('MY_CHANNEL_ID')
                                     ->message("Want to know more about the Birman cats ? Here!")
                                     ->components([new ActionRow([
                                         new LinkButtonComponent('Birman Wiki', 'https://en.wikipedia.org/wiki/Birman'),
                                         new LinkButtonComponent('Birman CFA', 'https://cfa.org/wp-content/uploads/2019/06/birman-standard.pdf'),
                                         new LinkButtonComponent('Birman FIFe', 'http://www1.fifeweb.org/dnld/std/SBI.pdf')
                                     ])]);
    }
}
```

### How to get a `channelId`

[How to find Discord IDs](https://www.remote.tools/remote-work/how-to-find-discord-id#how-to)

You must specify the actual ID of the channel when sending messages to the Discord API. This can be done directly in
the Discord client application by enabling developer tools.

---

# Interactions

Sometimes you may need to perform an action when a component is interacted with on Discord (Click on a button, menu item selected...). Using your own implementation of `ButtonComponent` or `SelectMenuInteractableComponent` you may execute your action.

## Setting up interactions

First setup a controller and/or route that you plan to use as the callback URL for interactions. Within the controller
for this route, inject the `Nwilging\LaravelDiscordBot\Contracts\Services\DiscordInteractionServiceInterface`.

You will call the `handleInteractionRequest` method on the aforementioned service. Example:

```php
use Nwilging\LaravelDiscordBot\Contracts\Services\DiscordInteractionServiceContract;

class MyController extends Controller
{
    private $interactionService;

    public function __construct(DiscordInteractionServiceContract $interactionService)
    {
        $this->interactionService = $interactionService;
    }
    
    public function handleDiscordInteraction(Request $request)
    {
        $response = $this->interactionService->handleInteractionRequest($request);
        return response()->json($response->toArray(), $response->getStatus());
    }
}
```
This will forward interactions requests from Discord through your app. **You must forward requests through this
interaction service:** Discord requires signature verification, which this package performs automatically on every
interactions request. Attempting to handle requests outside of this package is possible, but not recommended.

## Create your Components

You can extend `ButtonComponent` and `SelectMenuComponent` to create your own component and allows interactions.

### ButtonComponent

`ButtonComponent` allows to perform an action when the discord user click on a button.

```php
class MyCustomInteractableDiscordButton extends ButtonComponent
{
    public function onClicked(array $interactionRequest): void
    {
        // Execute your action
        \Log::info('This user clicked on the button');
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new DiscordInteractionReplyResponse('Performing the action.');
    }
}
```

Then use your custom `MyCustomInteractableDiscordButton` when sending a `DiscordMessage`

```php
class TestNotification extends Notification implements DiscordNotificationContract
{
    use Queueable;

    public function via($notifiable)
    {
        return ['discord'];
    }

    public function toDiscord($notifiable): DiscordMessage
    {
        return (new DiscordMessage())->channelId('MY_CHANNEL_ID')
                                     ->message("Do you want to perform the action ? Then click on the following button:")
                                     ->components([new ActionRow([
                                         new MyCustomInteractableDiscordButton('Click me')
                                     ])]);
    }
}
```

> When a Discord user will click on the `Click me` button, the bot will instantly answer `Performing the action.` as a reply to the initial message.

### SelectMenuInteractableComponent

`SelectMenuInteractableComponent` allows to perform an action when the discord user select an item on a drop-down menu.

```php
class MyCustomInteractableDiscordSelectMenu extends SelectMenuInteractableComponent
{

    public function __construct()
    {
        // Lets define some static options.
        $this->options = [
            new SelectOptionObject('Red', 'r'),
            new SelectOptionObject('Green', 'g'),
            new SelectOptionObject('Blue', 'b'),
        ];
    }
    
    public function onMenuItemsSubmitted(array $submittedValues, array $interactionRequest): void
    {
        // Execute your action
        
        // Example:
        if (in_array('g', $submittedValues)) {
            \Log::info('This user likes Green');
        }
        
        // At this point, $this->option includes all the option submitted
        // But not the non-submitted actions
        foreach ($this->options as $option) {
            \Log::info('This user selected :' . $option->value);
        }
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new DiscordInteractionReplyResponse('Performing the action.');
    }
}
```

Then use your custom `MyCustomInteractableDiscordSelectMenu` when sending a `DiscordMessage`
```php
class TestNotification extends Notification implements DiscordNotificationContract
{
    use Queueable;

    public function via($notifiable)
    {
        return ['discord'];
    }

    public function toDiscord($notifiable): DiscordMessage
    {
        $selectMenu = new MyCustomInteractableDiscordSelectMenu();
        $selectMenu->addOption(new SelectOptionObject('Yellow', 'y')) // We are adding an additional option for this select-menu.
                   ->withMinValues(1)
                   ->withMaxValues(3);
        return (new DiscordMessage())->channelId('MY_CHANNEL_ID')
                                     ->message("What color do you like ?")
                                     ->components([new ActionRow([
                                         $selectMenu
                                     ])]);
    }
}
```

> When a Discord user will have selected between 1 and 3 options, the bot will instantly answer `Performing the action.` as a reply to the initial message.



## Interaction Response

When `Discord` sends the interaction to your webhook, you can specify how your application is going to respond to this interaction using `getInteractionResponse` in your component.

#### Simple text reply example

```php
    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new DiscordInteractionReplyResponse('Thank you for this interaction');
    }
```

#### Reply with a modal and text-inputs

You can prompt a Discord modal to the user to ask for text inputs. See "@Modal interaction response" section.

#### Load while the interaction is processing

```php
    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new DiscordInteractionResponse(Component::LOAD_WHILE_HANDLING);
    }
```

#### Defer the response

```php
    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new DiscordInteractionResponse(Component::DEFER_WHILE_HANDLING);
    }
```

#### Default behavior

When the `getInteractionResponse` is not overriden or return `null`, the default behavior uses the `discord.php` configuration variable `interactions.component_interaction_default_behavior` which can be set to either `defer` or `load`.

# Modal interaction response

As a response to an interaction (Button or select-menu), you can prompt a modal to the user with a form that include between 1 and 5 text inputs.
You can achieve this using two seperate methods, depending on your needs.

## Using a GenericModal

Within your component, you can response with a modal without having to create your own modal class.
```php

class MyCustomInteractableDiscordButton extends ButtonComponent
{
    public function onClicked(array $interactionRequest): void
    {
        // Execute your action
        \Log::info('This user clicked on the button');
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
           return $this->createResponseModal('My Modal', [ new ShortTextInputComponent('Please enter your name', 'name') ]);
    }
    
    public function getInteractionResponseForResponseModal(GenericDiscordInteractionModalResponse $modal, array $interactionRequest): ?DiscordInteractionResponse {
            return new DiscordInteractionReplyResponse('Hi ' . $modal->getSubmitedValueForComponentWithParameter('name') . ', welcome !');
    }

    public function onResponseModalSubmitted(GenericDiscordInteractionModalResponse $modal, array $interactionRequest): void {
        
        \Log::info('This user clicked on the button and then submitted their name: ' . $modal->getSubmitedValueForComponentWithParameter('name'));
    }
}
```

## Creating your modal

You need to create your own custom modal
```php
class MyCustomModalComponent extends DiscordInteractionModalResponse
{
    public function __construct()
    {
        parent::__construct('Modal Title', [
            new ShortTextInputComponent('Please enter your name', 'name')
        ]);
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new DiscordInteractionReplyResponse('Noice.');
    }

    public function onModalSubmitted(array $interactionRequest): void
    {
        \Log::info('Hi, ' . $this->getSubmitedValueForComponentWithParameter('name') . ' and welcome.');
    }
}
```

Then your `MyCustomModalComponent` can be used as an `DiscordInteractionResponse` in any Components:

```php

class MyCustomInteractableDiscordButton extends ButtonComponent
{
    public function onClicked(array $interactionRequest): void
    {
        // Execute your action
        \Log::info('This user clicked on the button');
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new MyCustomModalComponent();
    }
}
```