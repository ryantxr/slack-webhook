# Slack Webhook

This library provides a client for Slack Webhooks. With minimal code, you should be able to send a message to a  channel.

This library does not try to do everything. It does one thing well.

It sends messages to Slack channels using Slack incoming webhooks.

This library uses Slack incoming webhooks. In the early days of slack, these were much easier to use than their API.
It was certainly easier to set up on the Slack platform. To some extent, this is still true.
There are no oauth permissions to set and the API itself is easier to understand.

## Motivation

I wanted a minimal library that can send a message with minimal effort.
Think of it as a paperclip. It is easy to use and requires almost zero learning to use it.
It should have very little complexity and require very little thinking to get it going.

This library is compatible with service containers.

## Getting Started

You can either copy the PHP files directly into your project or use composer.

Composer require command

`composer require ryantxr/slack-webhook`

## Features

* Minimal code to send a message
* Compatible with service containers
* Easy to use to any framework or even a plain php file

## Usage

```php
<?php

use Ryantxr\Slack\Webhook\Client as Webhook;

$webhook = new Webhook( 'YOUR_SLACK_WEBHOOK_URL' );
$webhook->message('This is a message');
// That's it!
```

### Multiple Webhooks / Channels

If you need to write to multiple channels, make a webhook for each channel.
Then, initialize the class like this:

```php
<?php

use Ryantxr\Slack\Webhook\Client as Webhook;

$config = [
    'channel1' => 'YOUR_SLACK_CHANNEL1_WEBHOOK_URL',
    'channel2' => 'YOUR_SLACK_CHANNEL2_WEBHOOK_URL'
];
$webhook = new Webhook( $config );
$webhook->channel('channel1')->message('This is a message');
```

## Slack Communicator

This is a new class that uses the Slack conversations API instead of using webhooks.
The only upside I can see is that it can be used to access multiple channels.

Here is how you use it.

```php
$slackToken = 'YOUR_SLACK_TOKEN';
$channelDescriptor = '{"channel_id": "C0000000008", "name": "random"}';
$channelDescriptor2 = '{"channel_id": "C1110000008", "name": "general"}';
use Ryantxr\Slack\Communicator\Client as SlackCommunicator;
$comm = new SlackCommunicator($slackToken);
// Set a default channel
$comm->channel($channelDescriptor);
// Send message to default channel
$comm->message('Some message');
// That's it!!

// Send to a different channel
$comm->message('Some message', $channelDescriptor2);
```

### Icons

It is possible to use different icons for the messages.
If you don't specify one, slack will use the default for the slack app.

There are two ways to specify an icon.

1. Emoji. ":rocket:"
2. URL. "https://somewhere/something"

You can specify a default icon or use a different icon for each message.

```php
$icon = ':blue_car:';
$comm->icon($icon); // set default

// Send message to default channel with a specific icon.
$comm->message('Some message', null, $icon);

// Send message to a specific channel with a specific icon.
$comm->message('Some message', $channelDescriptor2, $icon);
```

## Creating the communicator object

Construct a SlackCommunicator object from a container like this:

```php
function getObject() {
    // Assume there is some way to get the configuration
    $comm = (new SlackCommunicator(config('slack_token')))
        ->channel(config('slack_channel'))
        ->setIcon(config('slack_icon'));
    return $comm;
}
```
