<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;
use Ryantxr\Slack\Webhook\Client as WebhookClient;
/**
*  Corresponding Class to test YourClass class
*
*  @author yourname
*/
class ClientTest extends TestCase
{
    /**
     * Just check if the YourClass has no syntax error 
     *
     * This is just a simple check to make sure your library has no syntax error. This helps you troubleshoot
     * any typo before you even use this library in a real project.
     *
     */
    public function testIsThereAnySyntaxError()
    {
        $var = new \Ryantxr\Slack\Webhook\Client([]);
        $this->assertTrue(is_object($var));
        unset($var);
    }

    /**
     * Test setting a default channel
     */
    public function testSetDefaultChannel()
    {
        $client = new WebhookClient('https://my-webhook-url.com');
        $this->assertEquals('https://my-webhook-url.com', $client->getUrl());
    }

    /**
     * Test setting multiple channels
     */
    public function testSetMultipleChannels()
    {
        $client = new WebhookClient([
            'channel1' => 'https://webhook-url-1.com',
            'channel2' => 'https://webhook-url-2.com'
        ]);
        $this->assertEquals('https://webhook-url-1.com', $client->getWebhook('channel1'));
        $this->assertEquals('https://webhook-url-2.com', $client->getWebhook('channel2'));
    }

    /**
     * Test switching channels
     */
    public function testSwitchChannels()
    {
        $client = new WebhookClient([
            'channel1' => 'https://webhook-url-1.com',
            'channel2' => 'https://webhook-url-2.com'
        ]);
        $client->channel('channel2');
        $this->assertEquals('https://webhook-url-2.com', $client->getTempUrl());
    }

        /**
     * Test sending a message
     */
    public function testSendMessage()
    {
        // Create a mock HTTP client that implements the Psr\Http\Client\ClientInterface
        $mockHttpClient = $this->getMockForAbstractClass('Psr\Http\Client\ClientInterface');
        
        // Set up the mock to return a response with status code 200
        // Tell static analyzers to see this object as a MockObject
        /** @var \PHPUnit\Framework\MockObject\MockObject $mockHttpClient */
        $mockHttpClient->expects($this->once())
                       ->method('sendRequest')
                       ->willReturn(new \GuzzleHttp\Psr7\Response(200));

        // Create the client and set the mock HTTP client as the client property
        // post() is a protected method of the class, so make a new class
        // derived from that class and add a function to call the protected 
        // function I want to test.
        $client = new class('https://my-webhook-url.com') extends WebhookClient {
            public function callPost($data) { return $this->post($data); }
        };
        // Tell static analyzers to see this object as a ClientInterface
        /** @var \Psr\Http\Client\ClientInterface $mockHttpClient */
        $client->setClient($mockHttpClient);

        // Send a message and check the response code
        $this->assertEquals(200, $client->message('Test message')['code']);
    }

}
