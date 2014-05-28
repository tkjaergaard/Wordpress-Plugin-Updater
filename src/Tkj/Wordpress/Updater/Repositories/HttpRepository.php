<?php namespace Tkj\Wordpress\Updater\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Message\RequestInterface;

class HttpRepository implements UpdaterRepositoryInterface {

    /**
     * HTTP client
     *
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * Base url
     *
     * @var string
     */
    protected $url;

    /**
     * Headers to should be applied
     * to the request
     *
     * @var array
     */
    protected $headers = array();

    /**
     * @param string $url
     */
    public function __construct(Client $client, $url)
    {
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * Set header
     *
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * Remove header
     *
     * @param  string $name [description]
     */
    public function removeHeader($name)
    {
        if( isset($this->headers[$name]))
            unset( $this->headers[$name]);
    }

    /**
     * Get headers
     * @return mixed
     */
    public function getHeaders($name = null)
    {
        if( !$name)
            return $this->headers;

        if( isset($this->headers[$name]))
            return $this->headers[$name];

        return false;
    }

    /**
     * Get plugin info
     *
     * @return array
     */
    public function get($slug)
    {
        $url = "{$this->url}/{$slug}.json";

        return $this->makeRequest($url);
    }

    /**
     * Make api request
     *
     * @param  stirng $url
     * @return mixed
     */
    protected function makeRequest($url)
    {
        $request = $this->client->createRequest("GET", $url);

        // If there are headers, apply them to the request.
        if( $this->headers )
        {
            $request = $this->constructHeaders($request);
        }

        // Try fetching deta
        try {
            $response = $this->client->send($request);
        }
        catch(Exception $e)
        {
            return false;
        }

        return $response->json();
    }

    /**
     * Set headers
     *
     * @param  GuzzleHttp\Message\RequestInterface $request
     * @return GuzzleHttp\Message\RequestInterface
     */
    protected function constructHeaders(RequestInterface $request)
    {
        foreach($this->headers as $name => $value)
        {
            $request->setHeader($name, $value);
        }

        return $request;
    }
}
