# Slack Webhook

This library provides a client for Slack Webhooks. With minimal code, you should be able to send a message to a  channel.

## Motivation

I wanted a minimal library that can send a message with minimal effort.

This library is compatible with service containers.

## Getting Started

You can either copy the PHP files directly into your project or preferable just use composer.

Composer require command

`composer require ryantxr/slack-webhooks`

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
```
