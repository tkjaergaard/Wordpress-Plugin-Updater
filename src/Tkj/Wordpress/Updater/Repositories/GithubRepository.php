<?php namespace Tkj\Wordpress\Updater\Repositories;

use GuzzleHttp\Client;

class GithubRepository implements UpdaterRepositoryInterface {

    protected $url = 'https://api.github.com';

    /**
     * HTTP client
     *
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * Github owner
     *
     * @var string
     */
    protected $owner;

    /**
     * Github OAuth token
     *
     * @var string
     */
    public $token;

    /**
     * @param string $url
     */
    public function __construct(Client $client, $owner, $token = false)
    {
        $this->client = $client;
        $this->owner = $owner;
        $this->token = $token ?: getenv('GITHUB_TOKEN');
    }

    /**
     * Get plugin info
     *
     * @return array
     */
    public function get($slug)
    {
        $url = "{$this->url}/repos/{$this->owner}/{$slug}";

        $data = $this->makeRequest($url);

        $data['tags'] = $this->makeRequest($url."/tags");

        return $this->format( $data );
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

        // If a token isset set the authetication header.
        if( $this->token )
        {
            $request->setHeader('Authorization', $this->getAuthHeader());
        }

        // Try fetching deta from Github
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
     * Construct authentication header
     *
     * @return mixed
     */
    protected function getAuthHeader()
    {
        return sprintf('token %s', $this->token);
    }

    /**
     * Prepare output for Updater
     * ,
     * @param  array  $data
     * @return array
     */
    protected function format(array $data)
    {
        if( !isset($data['tags'][0]))
            return false;

        return $data = [
            'slug'           => $data['name'],
            'version'        => $data['tags'][0]['name'],
            'author'         => $data['owner']['login'],
            'download_link'  => $data['tags'][0]['zipball_url']."?access_token={$this->token}"
        ];
    }
}
