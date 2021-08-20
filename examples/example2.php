<?php
require __DIR__.'/../vendor/autoload.php';

use Ryantxr\Slack\Webhook\Client;


class ExampleApp1
{

    public function __construct($config)
    {
        $this->config = $config['webhooks'];
    }

    public function run($channel, $msg)
    {
        $this->client()->channel($channel)->message($msg);
    }
    
    public function client()
    {
        $client = new Client($this->config);
        return $client;
    }
}

$config = require __DIR__ . '/config.php';
if ( count($argv) > 2 ) {
    (new ExampleApp1($config))->run($argv[1], $argv[2]);
}