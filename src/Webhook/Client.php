<?php
namespace Ryantxr\Slack\Webhook;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class Client
{
	/**
	 * @var array<array-key,string>
	 */
	protected array $webhooks;
	protected ClientInterface $httpClient;

	/**
	 * @param string|array<array-key,string> $webhook Webhook URL or an array of key/value pairs for webhook URLs where the key is an alias or channel name for the webhook.
	 * @param ClientInterface|null $httpClient HTTP ClientInterface instance to send requests. Defaults to Guzzle.
	 */
	public function __construct(string|array $webhook, ?ClientInterface $httpClient = null)
	{
		if( \is_string($webhook) ){
			$this->webhooks["default"] = $webhook;
		}
		else {
			$this->webhooks = $webhook;
		}

		$this->httpClient = $httpClient ?: new Guzzle;
	}

	/**
	 * Send message to given Slack channel.
	 *
	 * @param string|array $message A string containing the message to send or an associative array of a full Slack message.
	 * @param string $channel The channel to send the message to. Defaults to "default".
	 * @see https://api.slack.com/reference/messaging/payload
	 * @return bool Returns true on successful send or false if message could not be sent.
	 */
	public function send(string|array $message, string $channel = "default"): bool
	{
		if ( !isset($this->webhooks[$channel]) ) {
			throw new ChannelException("Unknown channel \"" . $channel . "\".");
		}

		if ( \is_string($message) ) {
			$message = ["text" => $message];
		}

		$request = new Request(
			"post",
			$this->webhooks[$channel],
			["Content-Type" => "application/json"],
			\json_encode($message)
		);
		
		try {

			$response = $this->httpClient->sendRequest($request);
		}
		catch ( ClientExceptionInterface $clientException ) {
			return false;
		}

<<<<<<< HEAD
		if ( $response->getStatusCode() >= 300 ) {
=======
		if ( $response->getStatusCode() > 300 ) {
>>>>>>> 3a821ab6817bcb4365739042e2bade2a8b865a63
			return false;
		}

		return true;
	}
}