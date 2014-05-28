<?php

namespace spec\Tkj\Wordpress\Updater\Repositories;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HttpRepositorySpec extends ObjectBehavior
{
    public function Let($client, $request, $response)
    {
        $client->beADoubleOf('GuzzleHttp\\Client');
        $request->beADoubleOf('GuzzleHttp\\Message\\RequestInterface');
        $response->beADoubleOf('GuzzleHttp\\Message\\ResponseInterface');

        $this->beConstructedWith($client, 'foo');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Tkj\Wordpress\Updater\Repositories\HttpRepository');
    }

    function it_fetches_data_and_returns_array($client, $request, $response)
    {
        $client->createRequest(Argument::any(), Argument::any())->shouldBeCalled()->willReturn($request);

        $client->send(Argument::any())->shouldBeCalled()->willReturn($response);

        $response->json()->shouldBeCalled()->willReturn(array());

        $this->get('bar')->shouldBeArray();
    }

    function it_is_posible_to_set_header()
    {
        $this->setHeader('foo', 'bar');
        $this->getHeaders()->shouldBeArray(1);
        $this->getHeaders()->shouldHaveCount(1);
    }

    function it_is_posible_to_remove_header()
    {
        $this->setHeader('foo', 'bar');
        $this->getHeaders()->shouldHaveCount(1);

        $this->removeHeader('foo');
        $this->getHeaders()->shouldBeArray();
        $this->getHeaders()->shouldHaveCount(0);
    }

    function it_is_posible_to_get_a_single_header()
    {
        $this->setHeader('foo', 'bar');
        $this->getHeaders()->shouldbeArray(1);
        $this->getHeaders()->shouldHaveCount(1);

        $this->getHeaders('foo')->shouldBe('bar');
    }
}
