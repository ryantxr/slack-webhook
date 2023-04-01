<?php
namespace Ryantxr\Slack\Webhook;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Request;

/**
 * Configure a single default channel.
 *      new Client('a-default-channel-webhook')
 * Configure multiple channels
 *      new Client(['blah' => 'webhook1',
 *        'channel1-name' => 'channel1-webhook',
 *        'channel2-name' => 'channel2-webhook'
 *      ]);
 */
class Client
{
    /**
     * The default webhook URL to use if a single webhook is passed to the constructor.
     *
     * @var string|null
     */
    protected $url;

    /**
     * The temporary webhook URL to use if a channel-specific webhook is set using the `channel()` method.
     *
     * @var string|null
     */
    protected $tempUrl;

    /**
     * An array of channel-specific webhook URLs to use if an array of webhooks is passed to the constructor.
     *
     * @var array|null
     */
    protected $webhooks;

    /**
     * The Guzzle client to use for sending requests to Slack.
     *
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * Constructor
     *
     * @param string | array $arg takes a token or array of tokens
     */
    public function __construct($arg=null)
    {
        if ( is_array($arg) ) {
            if ( count($arg) > 0 ) {
                $i = 0;
                foreach ( $arg as $k => $v ) {
                    if ( $i == 0 ) {
                        $this->webhooks['default'] = $v;
                    }
                    $this->webhooks[$k] = $v;
                }
            }
        } elseif ( is_string($arg) ) {
            $this->webhooks['default'] = $arg;
            $this->url = $arg;
        }
        $this->client = new Guzzle;
    }

    /**
     * Set the client to use for http
     * @param \Psr\Http\Client\ClientInterface $client
     * @return self
     */
    public function setClient(\Psr\Http\Client\ClientInterface $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Switch channels
     *
     * @param string $channel which channel do you want
     * 
     * @return Client | null
     */
    public function channel(string $channel) : ?Client
    {
        if ( ! isset($this->webhooks[$channel]) ) {
            throw new \Exception("Unknown channel {$channel}");
        }
        $this->tempUrl = $this->webhooks[$channel];
        return $this;
    }

    /**
     * Send message to slack.
     *
     * @param string $text A string containing the message to send
     *
     * @return void
     */
    public function message($text)
    {
        $data = ['text' => $text];
        // Send as JSON
        return $this->post($data);
        // $this->post($text);
    }

    /**
     * Post to the incoming webhook endpoint.
     *
     * @param string $data the message to send
     *
     * @return array
     */

    protected function post($data): array
    {
        $url = ($this->tempUrl) ? $this->tempUrl : $this->url;
        $this->tempUrl = null; // put it back
        $response = null;
        if (is_string($data)) {
            $request = new Request('POST', $url);
            $response = $this->client->sendRequest(
                $request->withBody(\GuzzleHttp\Psr7\Utils::streamFor($data))
            );
        } elseif (is_array($data)) {
            $request = new Request('POST', $url, ['Content-Type' => 'application/json']);
            $response = $this->client->sendRequest(
                $request->withBody(\GuzzleHttp\Psr7\Utils::streamFor(json_encode($data)))
            );
        }
        if (is_object($response)) {
            $code = $response->getStatusCode(); // 200
            $reason = $response->getReasonPhrase(); // OK
            $body = $response->getBody();
        } else {
            $code = $reason = $body = null;
        }
    
        return compact('code', 'body', 'reason');
    }
     

     /*
    protected function post($data) : array
    {
        $url = ( $this->tempUrl ) ? $this->tempUrl : $this->url;
        $this->tempUrl = null; // put it back
        $response = null;
        if ( is_string($data) ) {
            $request = new Request('POST', $url);
            $response = $this->client->send(
                $request,
                [
                    'json' => ['text' => $data]
                ]
            );
        } elseif ( is_array($data) ) {
            $request = new Request('POST', $url, ['Content-Type' => 'application/json']);
            //print_r($data);
            $response = $this->client->send(
                $request,
                [
                    'json' => $data
                ]
            );
        }
        if ( is_object($response) ) {
            $code = $response->getStatusCode(); // 200
            $reason = $response->getReasonPhrase(); // OK
            $body = $response->getBody();
        } else {
            $code = $reason = $body = null;
        }

        return compact('code', 'body', 'reason');
    }
    */

    /**
     * Return the URL
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return the URL
     * @return string
     */
    public function getTempUrl()
    {
        return $this->tempUrl;
    }

    /**
     * Return a webhook
     * @return string|null
     */
    public function getWebhook($name)
    {
        return $this->webhooks[$name] ?? null;
    }
}