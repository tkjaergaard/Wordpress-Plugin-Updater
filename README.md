# DISCLAIMER

This is still work in progress. The API might change completely. Please don't use it yet.

---

# Custom Wordpress plugin updater

This package allows you to update your custom Wordpress plugin from a custom source
directly from the Wordpress plugin update interface.

## Installation

Clone this repository or install it through Composer to you custom plugin:

### Clone repository to `update` folder

    git clone {repo} updater

Now, you just have to require the necesarry files to e.g. `plugin.php`.
First you'll need to require the `Updater class`.

    require(__DIR__."/updater/src/Tkj/Wordpress/Updater/Updater.php");

Next you'll need to require your desired implementation of `UpdaterRepositoryInterface`.

### Install through Composer

Simply require the package in your `composer.json` file.

    "require": {
        "tkj/wp-updater": "1.0.*"
    }


## Usage

The package is fairly simple to use. All you'll have to do is create a new instance of `Tkj\Wordpress\Updater\Updater` and provide your configuration array and desired implementation of `UpdaterRepositoryInterface` to the constructor.

    <?php

    use Guzzlephp\Client;
    use Tkj\Wordpress\Updater\Repository\GithubRepository;
    use Tkj\Wordpress\Updater\Updater;

    $config = array(
        'slug' => 'my-custom-plugin',
        'version' => '1.0.0'
    );

    $client = new Guzzlephp\Client;

    $github = new GithubRepository($client, $owner, $token);
    $updater = new Updater($config, $github);

Now the updater will hook into the Wordpress native updater class and check if there is an update to your plugin through, in this case, Github.

## Included repositories

The package comes with some pre-build implementations to common services.

*Currently all included implementations needs an instance of the Guzzlehttp\Client to be injected.*

**These implementations are currently included:**

* Bitbucket
* Github
* Gitlab
* HTTP

Go to the [docs](http://docs.foo.bar/) to see implementation specific documentation.


## Custom implementations

You can easily create you own implementation to another service. All you'll have to do is implement the `Tkj\Wordpress\Updater\Updater\Repositories\RepositoryInterface` interface in your implementation.

    <?php namespace Acme\Wordpress\Updater\Repositories;

    use Tkj\Wordpress\Updater\Updater\Repositories\RepositoryInterface;

    class CustomRepository implements RepositoryInterface {

        public function get($slug)
        {
            // Return formatted array
        }

    }

Now just provide an instance of your custom implementation to the Updater class and you're all set to go.

## Author
Thomas Kjaergaard
Twitter: [@t_kjaergaard](https://twitter.com/t_kjaergaard)
Web: [tkjaergaard.dk](http://tkjaergaard.dk/)

## License
This package is released under the MIT license, which means you can do pretty much what ever you wanna do with it.

See `LICENSE` in this repository for more details.