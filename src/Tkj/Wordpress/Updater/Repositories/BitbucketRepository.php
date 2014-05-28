<?php namespace Tkj\Wordpress\Updater\Repositories;

use GuzzleHttp\Client;

class BitbucketRepository implements UpdaterRepositoryInterface {

    /**
     * HTTP client
     *
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * @param string $url
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get plugin info
     *
     * @return JSON
     */
    public function get($slug)
    {
        // Get data
    }
}