<?php

declare(strict_types=1);

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Ryantxr\Slack\Webhook\Client;
use Ryantxr\Slack\Webhook\Exception\AtLeastOneChannelNeededException;
use Ryantxr\Slack\Webhook\Exception\UnknownChannelException;
use function PHPUnit\Framework\assertSame;

class ClientTest extends TestCase
{
    public function testConstructorAndChannel()
    {
        $client = new Client('default');
        $client->channel('default');
        $this->expectException(UnknownChannelException::class);
        $client->channel('unknown');
        unset($client);
    }

    public function testEmptyChannels()
    {
        $this->expectException(AtLeastOneChannelNeededException::class);
        new Client();
    }

    public function testMessage()
    {
        $channel = 'some-channel';
        $guzzleMock = $this->createMock(ClientInterface::class);
        $guzzleMock
            ->expects(self::once())
            ->method('send')
            ->willReturn(new Response());
        $client = Client::constructWithHttpClient($guzzleMock, $channel);
        $response = $client->message('test');
        assertSame(200, $response->getStatusCode());
    }
}
