<?php
namespace Ryantxr\Slack\Webhook;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Request;
class Client
{
    protected $url;
    protected $tempUrl; // if this is set to a channel, use it.
    protected $webhooks;
    protected $client; // The guzzle client

    /**
     * Configure a single default channel.
     * 		new Client('a-default-channel-webhook')
     * Configure multiple channels
     * 		new Client(['blah' => 'webhook1', 
     * 			'channel1-name' => 'channel1-webhook',
     * 			'channel2-name' => 'channel2-webhook'
     * 		]);
     * @param string | array $arg
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
     * Switch channels
     * @param string $channel
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
     * message
     *
     * @param string $text A string containing the message to send
     *
     * @return void
     */
    public function message($text)
    {
        $data = ['text' => $text];
        // Send as JSON
        $this->post($data);
        // $this->post($text);
    }

    /**
     * post
     *
     * @param string $data the message to send
     *
     * @return array
     */
    protected function post($data) : array
    {
        // echo $this->url;
        // echo "\n";
        // exit;
        $url = ( $this->tempUrl ) ? $this->tempUrl : $this->url;
        $this->tempUrl = null; // put it back
        $response = null;
        if ( is_string($data) ) {
            $request = new Request('POST', $url);
            $response = $this->client->send($request, [
                'json' => [
                    'text' => $data
                    ]
                    ]);
        } elseif ( is_array($data) ) {
            $request = new Request('POST', $url, ['Content-Type' => 'application/json']);
            //print_r($data);
            $response = $this->client->send($request, [
            'json' => $data
            ]);
        }
        if ( is_object($response) ) {
            $code = $response->getStatusCode(); // 200
            $reason = $response->getReasonPhrase(); // OK
            $body = $response->getBody();
        } else {
            $code = $reason = $body = null;
        }

        // echo "code = $code\n";
        // echo "reason = $reason\n";
        // echo "body = $body\n";
        return compact('code', 'body', 'reason');
    }    
}