<?php
namespace Ryantxr\Slack\Communicator;

use Ryantxr\Slack\CanLog;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;

class Client
{
    use CanLog;

    /** @var Guzzle */
    private $client;

    /** @var string */
    private $slackToken;

    /** @var array|string|null */
    private $defaultIcon;

    /**
     * SlackCommunicator constructor.
     *
     * @param string $slackToken
     * @param string|array|null $icon
     */
    public function __construct($slackToken, $icon = null)
    {
        $this->client = new Guzzle(['base_uri' => 'https://slack.com/api/']);
        $this->slackToken = $slackToken;
        $this->setIcon($icon);
    }

    /**
     * Sends a message to the specified channel.
     *
     * @param string $channelName
     * @param string $message
     * @param string|array|null $icon
     * @return bool
     * @throws \Exception
     */
    public function sendMessage($channelName, $message, $icon = null)
    {
        // Get the channel ID for the specified channel name
        $channelId = $this->getChannelId($channelName);

        // If the channel ID is null, throw an exception
        if ($channelId === null) {
            throw new \Exception('Channel not found.');
        }

        // Did we get an icon or do we use the default?
        $icon = ($icon == null) ? $this->defaultIcon : $icon;

        $params = [
            'channel' => $channelId,
            'text' => $message,
        ];
        
        // Determine if the icon is an emoji or URL and set the appropriate parameter
        if ( ! empty($icon) ) {
            if (preg_match('/^:.+:$/', $icon)) {
                $iconParam = ['icon_emoji' => $icon];
                $this->debug("Using emoji {$icon}");
            } else {
                $iconParam = ['icon_url' => $icon];
                $this->debug("Using url {$icon}");
            }
            $params = array_merge($params, $iconParam);
        }
        $this->debug("params " . json_encode($params));
        try {
            $response = $this->client->request('POST', 'chat.postMessage', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->slackToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $params,
            ]);

            $responseBody = json_decode($response->getBody(), true);
            if ($responseBody['ok']) {
                return true;
            } else {
                throw new \Exception($responseBody['error']);
            }
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Returns the ID of the specified channel.
     * There are multiple ways to specify a channel.
     * 1. Pass in a name like 'random' and this function will
     *    try to look it up based on that name. This seems to
     *    have issues with private channels.
     * 2. Send in a channel id by prefixing a '.'. For example, '.CCCCCC'.
     *    When this function sees the prefixed '.' it will remove the dot
     *    and use the channel id.
     * 3. Send in a JSON object like this: 
     *    This function will get the channel id from the object.
     *    The benefit of using this approach is that it is self documenting.
     *    If you just saw '.CCCCCC' it would not be obvious that it is a channel id.
     * @param string $channelName
     * @return string|null
     * @throws \Exception
     */
    private function getChannelId($channelName)
    {
        // Check if the channel ID can be parsed as JSON
        $decoded = json_decode($channelName);
        if (is_object($decoded) && isset($decoded->channel_id)) {
            return $decoded->channel_id;
        }

        // If the channel name starts with a '.', use the rest of the string as the channel ID
        if (substr($channelName, 0, 1) == '.') {
            return substr($channelName, 1);
        }

        try {
            $response = $this->client->request('GET', 'conversations.list', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->slackToken,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            if ($responseBody['ok']) {
                foreach ($responseBody['channels'] as $channel) {
                    if ($channel['name'] == $channelName) {
                        return $channel['id'];
                    }
                }
            } else {
                throw new \Exception($responseBody['error']);
            }

            // If the channel was not found, return null
            return null;
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Sets the default icon to be used in messages.
     *
     * @param string|array|null $icon
     * @return Client
     */
    public function setIcon($icon)
    {
        $this->defaultIcon = $icon;
        return $this;
    }
}
