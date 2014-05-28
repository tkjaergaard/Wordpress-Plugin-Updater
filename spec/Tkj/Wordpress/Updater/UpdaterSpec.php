<?php namespace spec\Tkj\Wordpress\Updater;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tkj\Wordpress\Updater\Repositories\UpdaterRepositoryInterface;

class UpdaterSpec extends ObjectBehavior
{

    public function __construct()
    {
        require_once(realpath(__DIR__."/../../../../bootstrap.test.php"));
    }

    protected function getConfig()
    {
        return [
            'slug' => 'foo',
            'path' => 'bar',
            'version' => '1.0.0'
        ];
    }

    function Let(UpdaterRepositoryInterface $interface)
    {
        $this->beConstructedWith(
            $this->getConfig(),
            $interface
        );
    }

    function it_should_not_throw_exception_if_the_config_is_valid(UpdaterRepositoryInterface $interface)
    {
        $this->shouldNotThrow('\InvalidArgumentException')->during('__construct', [$this->getConfig(), $interface]);
    }

    function it_should_throw_exception_if_the_config_is_not_valid(UpdaterRepositoryInterface $interface)
    {
        $this->shouldThrow('\InvalidArgumentException')->during('__construct', [[], $interface]);
    }

    function it_should_be_instantiable(UpdaterRepositoryInterface $interface)
    {
        $this->shouldHaveType('Tkj\Wordpress\Updater\Updater');
    }

    function it_should_return_true_if_update_is_available(UpdaterRepositoryInterface $interface)
    {
        $interface->get('foo')->willReturn(['version' => '1.0.1']);
        $this->shouldHaveUpdate();
    }

    function it_should_return_false_if_no_update_is_available(UpdaterRepositoryInterface $interface)
    {
        $interface->get('foo')->willReturn(['version' => '0.9.9']);
        $this->shouldNotHaveUpdate();

        $interface->get('foo')->willReturn(['version' => '1.0.0']);
        $this->shouldNotHaveUpdate();
    }
}