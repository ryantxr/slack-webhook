<?php
namespace Ryantxr\Slack\Webhook;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Request;
class Client
{
    protected $url;
	protected $tempUrl; // if this is set to a channel, use it.
	protected $webhooks;
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
    }

	/**
	 * Switch channels
	 */
	public function channel(string $channel)
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
	 * 
	 * 
	 *
	 * @param string $message the message to send
	 *
	 * @return string
	 */
	protected function post($data)
	{
        // echo $this->url;
        // echo "\n";
        // exit;
		$client = new Guzzle;
		$url = ( $this->tempUrl ) ? $this->tempUrl : $this->url;
		$this->tempUrl = null; // put it back
		if ( is_string($data) ) {
			$request = new Request('POST', $url);
			$response = $client->send($request, [
				'json' => [
					'text' => $data
					]
					]);
		} elseif ( is_array($data) ) {
			$request = new Request('POST', $url, ['Content-Type' => 'application/json']);
			//print_r($data);
			$response = $client->send($request, [
			'json' => $data
			]);
		}

		$code = $response->getStatusCode(); // 200
		$reason = $response->getReasonPhrase(); // OK
		$body = $response->getBody();

		// echo "code = $code\n";
		// echo "reason = $reason\n";
		// echo "body = $body\n";
		return compact('code', 'body', 'reason');
	}    
}