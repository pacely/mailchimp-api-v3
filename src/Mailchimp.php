<?php

namespace Mailchimp;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;

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
     * @param $resource
     * @param array $arguments
     * @return string
     */
    public function request($resource, $arguments = [], $method = 'GET')
    {
        $method = strtolower($method);

        $resource = $this->decorateResource($resource, $arguments, $method);

        return $this->call($resource, $arguments, $method);
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
            $request = $this->client->{$method}($this->endpoint . $resource, [
                'headers' => [
                    'Authorization' => 'apikey ' . $this->apikey,
                    'Content-type'  => 'application/json'
                ],
                'body'    => json_encode($arguments)
            ]);

            $collection = new Collection($request->json());

            return $method == 'get' ? $collection->collapse() : $collection;

        } catch (RequestException $e) {
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
     * @param $resource
     * @param $arguments
     * @param $method
     * @return string
     */
    private function decorateResource($resource, $arguments, $method)
    {
        if ($method == 'get') {
            $resource .= '?' . http_build_query($arguments);
        }

        return $resource;
    }

}
