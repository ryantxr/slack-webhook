<?php
namespace Ryantxr\Slack\Webhook;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Request;
class Client
{
    protected $url;
    public function __construct($arg=null)
    {
        if ( is_string($arg) ) {
            $this->url = $arg;
        }
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
		
		if ( is_string($data) ) {
			$request = new Request('POST', $this->url);
			$response = $client->send($request, [
				'json' => [
					'text' => $data
					]
					]);
		} elseif ( is_array($data) ) {
			$request = new Request('POST', $this->url, ['Content-Type' => 'application/json']);
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