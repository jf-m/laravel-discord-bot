<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBot\Support\Embeds;

use Nwilging\LaravelDiscordBot\Support\Embed;
use Nwilging\LaravelDiscordBot\Support\Traits\MergesArrays;

/**
 * Embed Object
 * @see https://discord.com/developers/docs/resources/channel#embed-object-embed-structure
 */
abstract class EmbedObject
{
    abstract public function toArray(): array;
}
