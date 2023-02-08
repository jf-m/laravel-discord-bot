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
        $embed = new Embed('Title', 'Description', '2022-10-21T00:00:00.000Z');
        $embed->withColor(16711680) // Red
              ->withImage(new ImageEmbed('http://image.link.co'))
              ->withAuthor(new AuthorEmbed('Author name', 'http://author.co'))
              ->withFooter(new FooterEmbed('Footer', 'http://footer.co'))
              ->withField((new FieldEmbed('Field1', 'Value1'))->inline())
              ->withField((new FieldEmbed('Field2', 'Value2'))->inline());

        return (new DiscordMessage())->channelId('MY_CHANNEL_ID')
                                     ->message("Want to know more about stuff? It's here:")
                                     ->embeds([$embed]);
    }
}
```

## Components

Each `DiscordMessage` can include multiple `LinkButtonComponent`, `ButtonComponent` or `SelectMenuComponent`

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
                                     ->message("Want to know more about stuff ? Here!")
                                     ->components([new ActionRow([
                                         new LinkButtonComponent('Document 1', 'https://doc.1.co'),
                                         new LinkButtonComponent('Document 2', 'https://doc.2.co'),
                                         new LinkButtonComponent('Link 3', 'https://link.3.co')
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

Using your own implementation of `ButtonComponent` or `SelectMenuInteractableComponent`, you may perform an action when a component is interacted with.

## Setting up interactions

Create a controller and a route for your webhook. Within the controller, inject the `Nwilging\LaravelDiscordBot\Contracts\Services\DiscordInteractionServiceInterface`.
You must call the `handleInteractionRequest` method on this service. Example:

```php
use Nwilging\LaravelDiscordBot\Contracts\Services\DiscordInteractionServiceContract;

class DiscordWebhookController extends Controller
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

```php 
    Route::post('discord/interactions', 'DiscordWebhookController@handleInteractionRequest');
```

Don't forget to set your `INTERACTIONS ENDPOINT URL` to point this webhook within your Discord App `General Information` setting.

> This will forward interactions requests from Discord through your app. **You must forward requests through this interaction service:** Discord requires signature verification, which this package performs automatically on every interactions request. Attempting to handle requests outside of this package is possible, but not recommended.

### Test your webhooks in localhost
There is several tools available on the internet to relay discord interactions.

To name a few:

- [Webhook Relay](https://webhookrelay.com/)
- [ngrok](https://ngrok.com)

## Create your Interactable Components

You can extend `ButtonComponent` and `SelectMenuComponent` to create your own component and allows interactions.

### ButtonComponent

`ButtonComponent` allows to perform an action when the discord user click on a button.

```php
class MyCustomInteractableDiscordButton extends ButtonComponent implements ShouldQueue
{
    public function onClicked(array $interactionRequest): void
    {
        // Execute your action
        \Log::info('Someone clicked on the button');
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new DiscordInteractionReplyResponse('Performing the action.');
    }
}
```

Then use your new Button when sending a `DiscordMessage`

```php
    public function toDiscord($notifiable): DiscordMessage
    {
        return (new DiscordMessage())->channelId('MY_CHANNEL_ID')
                                     ->message("Do you want to perform the action ? Then click on the following button:")
                                     ->components([new ActionRow([
                                         new MyCustomInteractableDiscordButton('Click me')
                                     ])]);
    }
```

> When a Discord user will click on the `Click me` button, the bot will instantly answer `Performing the action.` as a reply to the initial message.

### SelectMenuInteractableComponent

This component allows to perform an action when the discord user select an item on a drop-down menu.

```php
class MyCustomInteractableDiscordSelectMenu extends SelectMenuInteractableComponent implements ShouldQueue
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

Then use your new component when sending a `DiscordMessage`
```php
    public function toDiscord($notifiable): DiscordMessage {
        $selectMenu = new MyCustomInteractableDiscordSelectMenu();
        $selectMenu->addOption(new SelectOptionObject('Yellow', 'y')) // We can add additional options here
                   ->withMinValues(1)
                   ->withMaxValues(3);
        return (new DiscordMessage())->channelId('MY_CHANNEL_ID')
                                     ->message("What color do you like ?")
                                     ->components([new ActionRow([
                                         $selectMenu
                                     ])]);
    }
```

> When a Discord user will have selected between 1 and 3 options, the bot will instantly answer `Performing the action.` as a reply to the initial message.


## Interaction Response

When `Discord` sends the interaction to your webhook, you can specify how your application is going to respond to this interaction using `getInteractionResponse` in your component.

### Simple text reply example

```php
    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse {
        return new DiscordInteractionReplyResponse('Thank you for this interaction');
    }
```

### Load while the interaction is processing

```php
    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse {
        return new DiscordInteractionResponse(Component::LOAD_WHILE_HANDLING);
    }
```

### Defer the response

```php
    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse {
        return new DiscordInteractionResponse(Component::DEFER_WHILE_HANDLING);
    }
```

### Reply with a modal and text-inputs

You can prompt a Discord modal to the user to ask for text inputs. See [Modal interaction response](#modal-interaction-response)

### Default behavior

When the `getInteractionResponse` is not overriden or return `null`, the default behavior uses the config `config('discord.interactions.component_interaction_default_behavior')` which can be set to either `defer` or `load`.

## Modal interaction response

As a response to an interaction (Button or select-menu), you can prompt a modal to the user with a form that include between 1 and 5 text inputs.
You can achieve this through two separate methods, depending on your needs.

### Using a GenericModal

The simplest way is by using a GenericModal directly within your component. By doing so, you can response with a modal without having to create your own modal class.

```php

class MyCustomInteractableDiscordButton extends ButtonComponent implements ShouldQueue {
    public function onClicked(array $interactionRequest): void {
        // Execute your action
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse {
        // As a response to the click on the button, the modal "My Modals" will pop asking the user his name.
        return $this->createResponseModal('My Modals', [ new ShortTextInputComponent('Please enter your name', 'name') ]);
    }
    
    public function getInteractionResponseForResponseModal(GenericDiscordInteractionModalResponse $modal, array $interactionRequest): ?DiscordInteractionResponse {
        // When the user submits the modal, we reply to him directly, using the value they just submitted. 
        return new DiscordInteractionReplyResponse('Hi ' . $modal->getComponentWithActionValue('name') . ', welcome !');
    }

    public function onResponseModalSubmitted(GenericDiscordInteractionModalResponse $modal, array $interactionRequest): void {
        // We can then perform an action that will use the user input
        \Log::info('Hi, ' . $modal->getComponentWithActionValue('name') . ' and welcome.');
    }
}
```

### Creating your modal

For more complex scenarios, you can create your own class for the modal behavior.

```php
class MyCustomModalComponent extends DiscordInteractionModalResponse
{
    public function __construct()
    {
        parent::__construct('Modals Title', [
            (new ShortTextInputComponent('Please enter your name')->withAction('name')
        ]);
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new DiscordInteractionReplyResponse('Hi ' . $this->getComponentWithActionValue('name') . ', welcome !');
    }

    public function onModalSubmitted(array $interactionRequest): void
    {
        \Log::info('Hi, ' . $this->getComponentWithActionValue('name') . ' and welcome.');
    }
}
```

Then your `MyCustomModalComponent` can be used as an `DiscordInteractionResponse` in any Components, for instance a button:

```php

class MyCustomInteractableDiscordButton extends ButtonComponent implements ShouldQueue
{
    public function onClicked(array $interactionRequest): void
    {
        // Execute your action on click
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new MyCustomModalComponent();
    }
}
```

## Interaction Action Name / Action Value

When your `Component` or your `Modal` is sent to Discord, its classname is serialized along with its public property `actionName` and `actionValue` and these serialized value are passed to Discord. When Discord send an interaction to your webhook, the original `Component` or `Model` class is recreated, and its public property `actionValue` and `actionName` are populated.
These `actionValue` and `actionName` can be used at your convenience to save data between interactions. Example:

```php
class ArchiveArticleDiscordButton extends ButtonComponent implements ShouldQueue
{
    public function onClicked(array $interactionRequest): void
    {
        $article = Article::find($this->actionValue);
        $article->archive();
    }

    public function getInteractionResponse(array $interactionRequest): ?DiscordInteractionResponse
    {
        return new DiscordInteractionReplyResponse('Your article will be archived in a few moments.');
    }
}
```

```php
    public function toDiscord($notifiable): DiscordMessage
    {
        $oldArticles = Article::where('created_at', '<=', Carbon::now()->subYear())->get();
        $actionRow = new ActionRow();
        foreach($oldArticles as $article) {
            $actionRow->addComponent((new ArchiveArticleDiscordButton(label: $article->name))->withAction($article->id));
        }
        return (new DiscordMessage())->channelId('MY_CHANNEL_ID')
                                     ->message("The following articles are old. Click on the ones you want to archive:")
                                     ->components([$actionRow]);
    }
```

### Parameter Limitation
Discord only allows up to [100 characters](https://discord.com/developers/docs/interactions/message-components#custom-id) inside the `custom_id` field. Therefore, in some rare cases, the serialized classname and parameter can exceed the 100 characters limit.
An exception will be thrown if this is the case, to inform you that the action will not be possible.
An easy way to reduce the size of the `custom_id` field is by adding the namespaces of your custom Components and Modal inside the configuration file.

```php
        /**
         * Namespaces of your custom components and modals
         * This is used to reduce the length of the custom_id parameter
         */
        'namespaces' => [
            "App\\",
            "Nwilging\\LaravelDiscordBot\\Support\\Components\\",
            "Nwilging\\LaravelDiscordBot\\Support\\Interactions\\Responses\\"
        ],
```
> By default, `App\\` is already configured, but if you decide to create a deeper namespace such as `App\MyProject\Discord\Components\`, then it's highly recommmended to append this namespace to your config file.


## Queuing Interactions

Your Interactable Components must implement the `ShouldQueue` interface in order to be processed in a queued job.

You can also define for each instance of any component a specific `queue` or `connection`:


```php
    public function toDiscord($notifiable): DiscordMessage
    {
        return (new DiscordMessage())->channelId('MY_CHANNEL_ID')
                                     ->message("Do you want to perform the action ? Then click on the following button:")
                                     ->components([new ActionRow([
                                         (new MyCustomInteractableDiscordButton('Click me'))->onConnection('redis')->onQueue('high-priority')
                                     ])]);
    }
```
> The `queue` and `connection` parameter can also be overriden within the class itself.
