<?php

declare(strict_types=1);

namespace Ryantxr\Slack\Webhook;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Ryantxr\Slack\Webhook\Exception\AtLeastOneChannelNeededException;
use Ryantxr\Slack\Webhook\Exception\UnknownChannelException;

class Client
{
    protected string $currentChannelWebhook;
    /** @var string[] $webhooks */
    protected array $webhooks;
    protected ClientInterface $client;

    /**
     * Expects webhooks as variadic (comma separated arguments)
     * new Client('a-default-channel-webhook', 'channel1-webhook', 'channel2-webhook')
     * @throws AtLeastOneChannelNeededException
     */
    final public function __construct(string ...$webhooks)
    {
        if ([] === $webhooks) {
            throw new AtLeastOneChannelNeededException();
        }
        $this->webhooks = $webhooks;
        $this->currentChannelWebhook = $this->webhooks[0];
        $this->client = new Guzzle();
    }

    /** @throws AtLeastOneChannelNeededException */
    public static function constructWithHttpClient(ClientInterface $client, string ...$webhooks): self
    {
        $self = new static(...$webhooks);
        $self->client = $client;
        return $self;
    }

    /**
     * Switch channels
     * @throws UnknownChannelException
     */
    public function channel(string $channel): Client
    {
        if (!in_array($channel, $this->webhooks)) {
            throw new UnknownChannelException("Unknown channel {$channel}");
        }
        $this->currentChannelWebhook = $channel;
        return $this;
    }

    /** @throws GuzzleException */
    public function message(string $text): ResponseInterface
    {
        return $this->client->send(
            new Request(
                'POST',
                $this->currentChannelWebhook,
                ['Content-Type' => 'application/json']
            ),
            [
            'json' => ['text' => $text],
            ]
        );
    }
}
