<?php
require __DIR__.'/../vendor/autoload.php';

use Ryantxr\Slack\Webhook\Client;


class ExampleApp1
{
    public $config;

    public function __construct($config)
    {
        $this->config = $config['webhook'];
        print_r($this->config);
    }

    public function run($msg)
    {
        $this->client()->message($msg);
    }
    
    public function client()
    {
        $client = new Client($this->config['url']);
        return $client;
    }
}

$config = include __DIR__ . '/config.php';
if ( count($argv) > 1 ) {
    (new ExampleApp1($config))->run($argv[1]);
}