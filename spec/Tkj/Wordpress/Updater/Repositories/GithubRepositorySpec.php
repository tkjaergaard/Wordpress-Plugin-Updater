<?php

namespace spec\Tkj\Wordpress\Updater\Repositories;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use GuzzleHttp\Client;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

class GithubRepositorySpec extends ObjectBehavior
{
    function Let(Client $client, RequestInterface $request, ResponseInterface $resonse)
    {
        $this->beConstructedWith($client, 'foo');
    }


    function it_should_recive_data_from_github(Client $client, RequestInterface $request, RequestInterface $request2, ResponseInterface $response, ResponseInterface $response2)
    {
        $response->json()->shouldBeCalledTimes(1)->willReturn([
            'name' => 'foo',
            'owner' => ['login' => 'bar']
        ]);

        $client->createRequest('GET', 'https://api.github.com/repos/foo/bar')->shouldBeCalledTimes(1)->willReturn($request);
        $client->send($request)->shouldBeCalledTimes(1)->willReturn($response);

        $response2->json()->shouldBeCalledTimes(1)->willReturn([
            [
                'name' => '1.0.0',
                'zipball_url' => 'foo'
            ]
        ]);

        $client->createRequest('GET', 'https://api.github.com/repos/foo/bar/tags')->shouldBeCalledTimes(1)->willReturn($request2);
        $client->send($request2)->shouldBeCalledTimes(1)->willReturn($response2);

        $this->shouldHaveType('Tkj\Wordpress\Updater\Repositories\GithubRepository');

        $this->get('bar')->shouldBeArray();

    }

    function it_should_return_false_if_no_tags_was_recived_from_github(Client $client, RequestInterface $request, RequestInterface $request2, ResponseInterface $response, ResponseInterface $response2)
    {
        $response->json()->shouldBeCalledTimes(1)->willReturn([
            'name' => 'foo',
            'owner' => ['login' => 'bar']
        ]);

        $client->createRequest('GET', 'https://api.github.com/repos/foo/bar')->shouldBeCalledTimes(1)->willReturn($request);
        $client->send($request)->shouldBeCalledTimes(1)->willReturn($response);

        $response2->json()->shouldBeCalledTimes(1)->willReturn([]);

        $client->createRequest('GET', 'https://api.github.com/repos/foo/bar/tags')->shouldBeCalledTimes(1)->willReturn($request2);
        $client->send($request2)->shouldBeCalledTimes(1)->willReturn($response2);

        $this->shouldHaveType('Tkj\Wordpress\Updater\Repositories\GithubRepository');

        $this->get('bar')->shouldBe(false);

    }

    function it_should_not_have_a_token()
    {
        $this->token->shouldBe(false);

    }

    function it_should_have_a_token(Client $client)
    {
        $this->beConstructedWith($client, 'foo', 'bar');
        $this->token->shouldBe('bar');

    }

    function it_should_have_ability_to_set_a_token()
    {
        $this->token->shouldBe(false);

        $this->token = 'bar';

        $this->token->shouldBe('bar');

    }
}
