<?php

namespace Mailchimp;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;

/**
 * @method Collection get($resource, array $options = [])
 * @method Collection head($resource, array $options = [])
 * @method Collection put($resource, array $options = [])
 * @method Collection post($resource, array $options = [])
 * @method Collection patch($resource, array $options = [])
 * @method Collection delete($resource, array $options = [])
 */
class Mailchimp
{

    /**
     * Endpoint for Mailchimp API v3
     *
     * @var string
     */
    private $endpoint = 'https://us1.api.mailchimp.com/3.0/';

    /**
     * @var string
     */
    private $apikey;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param string $apikey
     */
    public function __construct($apikey = '')
    {
        $this->apikey = $apikey;
        $this->client = new Client();

        if (strstr($this->apikey, '-')) {
            list(, $dc) = explode('-', $this->apikey);
            $this->endpoint = str_replace('us1', $dc, $this->endpoint);
        }
    }

    /**
     * @param string $method
     * @param string $resource
     * @param array $arguments
     * @return string
     */
    public function request($resource, $arguments = [], $method = 'GET')
    {
        return $this->call($resource, $arguments, strtolower($method));
    }

    /**
     * @param $resource
     * @param array $arguments
     * @param string $method
     * @return string
     * @throws Exception
     */
    private function call($resource, $arguments, $method)
    {
        try {
            $options = $this->getOptions($method, $arguments);
            $response = $this->client->{$method}($this->endpoint . $resource, $options);

            $collection = new Collection(json_decode($response->getBody()));

            if ($collection->count() == 1) {
                return $collection->collapse();
            }

            return $collection;

        } catch (RequestException $e) {
            throw new Exception($e->getResponse()->getBody());
        } catch (ClientException $e) {
            throw new Exception($e->getResponse()->getBody());
        }
    }

    /**
     * Return endpoint
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param $method
     * @param $arguments
     * @return array
     */
    private function getOptions($method, $arguments)
    {
        $options = [
            'headers' => [
                'Authorization' => 'apikey ' . $this->apikey
            ]
        ];

        if (empty($arguments)) {
            return $options;
        }

        if ($method == 'get') {
            $options['query'] = $arguments;
        } else {
            $options['json'] = $arguments;
        }

        return $options;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return Collection
     */
    public function __call($method, $arguments)
    {
        if (count($arguments) < 1) {
            throw new \InvalidArgumentException('Magic request methods require a URI and optional options array');
        }

        $resource = $arguments[0];
        $options = isset($arguments[1]) ? $arguments[1] : [];

        return $this->request($resource, $options, $method);
    }
}
