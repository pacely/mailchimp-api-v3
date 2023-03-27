<?php

namespace Mailchimp;

use BadMethodCallException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class Mailchimp
{
    /**
     * Endpoint for Mailchimp API v3.
     */
    private string $endpoint = 'https://us1.api.mailchimp.com/3.0/';

    private string $apikey;

    private Client $client;

    /**
     * @var string[]
     */
    private array $allowedMethods = ['get', 'head', 'put', 'post', 'patch', 'delete'];

    /**
     * @var array<string,mixed>
     */
    public array $options = [];

    /**
     * @param array<string,mixed> $clientOptions
     */
    public function __construct(string $apikey = '', array $clientOptions = [])
    {
        $this->apikey = $apikey;
        $this->client = new Client($clientOptions);

        $this->detectEndpoint($this->apikey);

        $this->options['headers'] = [
            'Authorization' => 'apikey '.$this->apikey,
        ];
    }

    /**
     * @throws Exception
     */
    public function request(string $resource, array $arguments = [], string $method = 'GET'): Collection
    {
        if (! $this->apikey) {
            throw new Exception('Please provide an API key.');
        }

        return $this->makeRequest($resource, $arguments, strtolower($method));
    }

    /**
     * Enable proxy if needed.
     */
    public function setProxy(
        string $host,
        int $port,
        bool $ssl = false,
        ?string $username = null,
        ?string $password = null
    ): string {
        $scheme = ($ssl ? 'https://' : 'http://');

        if (! is_null($username)) {
            return $this->options['proxy'] = sprintf('%s%s:%s@%s:%s', $scheme, $username, $password, $host, $port);
        }

        return $this->options['proxy'] = sprintf('%s%s:%s', $scheme, $host, $port);
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function detectEndpoint(string $apikey): void
    {
        if (! strstr($apikey, '-')) {
            throw new InvalidArgumentException('There seems to be an issue with your apikey. Please consult Mailchimp');
        }

        list(, $dc) = explode('-', $apikey);
        $this->endpoint = str_replace('us1', $dc, $this->endpoint);
    }

    public function setApiKey(string $apikey): void
    {
        $this->detectEndpoint($apikey);

        $this->apikey = $apikey;
    }

    /**
     * @throws Exception
     */
    private function makeRequest(string $resource, array $arguments, string $method): Collection
    {
        try {
            $options = $this->getOptions($method, $arguments);
            $response = $this->client->{$method}($this->endpoint.$resource, $options);

            $collection = new Collection(
                json_decode($response->getBody())
            );

            if ($collection->count() == 1) {
                return $collection->collapse();
            }

            return $collection;
        } catch (ClientException $e) {
            throw new Exception($e->getResponse()->getBody());
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if ($response instanceof ResponseInterface) {
                throw new Exception($response->getBody());
            }

            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return array<string,mixed>
     */
    private function getOptions(string $method, array $arguments): array
    {
        unset($this->options['json'], $this->options['query']);

        if (count($arguments) < 1) {
            return $this->options;
        }

        if ($method == 'get') {
            $this->options['query'] = $arguments;
        } else {
            $this->options['json'] = $arguments;
        }

        return $this->options;
    }

    /**
     * @throws Exception
     */
    public function __call(string $method, array $arguments): Collection
    {
        if (count($arguments) < 1) {
            throw new InvalidArgumentException('Magic request methods require a URI and optional options array');
        }

        if (! in_array($method, $this->allowedMethods)) {
            throw new BadMethodCallException('Method "'.$method.'" is not supported.');
        }

        $resource = $arguments[0];
        $options = $arguments[1] ?? [];

        return $this->request($resource, $options, $method);
    }
}
