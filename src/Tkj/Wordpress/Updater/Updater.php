<?php namespace Tkj\Wordpress\Updater;

use Tkj\Wordpress\Updater\Repositories\UpdaterRepositoryInterface;
use InvalidArgumentException;

class Updater implements UpdaterInterface {

    /**
     * @var Tkj\Wordpress\Updater\UpdaterRepositoryInterface;
     */
    protected $repo;

    /**
     * Plugin config
     *
     * @var Array
     */
    protected $config;

    /**
     * Package info from repository
     *
     * @var array
     */
    protected $package;

    /**
     * Plugin options from Wordpress
     *
     * @var object
     */
    protected $plugin;

    /**
     * @param array                     $config
     * @param UpdaterReposiotyInterface $repo
     */
    public function __construct(array $config, UpdaterRepositoryInterface $repo)
    {
        // Validate config and set config
        if( $this->validateConfig($config))
            $this->config = $config;

        // Set repository instance
        $this->repo = $repo;

        // Get plugin options from Wordpress
        $this->plugin = $this->getOptions();

        add_action('load-update-core.php', function()
        {
            $this->update(true);
        });
    }

    /**
     * Check API for update
     *
     * @return boolean
     */
    public function hasUpdate()
    {
        if ( !current_user_can('update_plugins') OR !$package = $this->getPackage())
        {
            return false;
        }

        return version_compare($this->config['version'], $package['version'], '<');
    }

    /**
     * Update the package
     *
     * @param  boolean $force
     * @return array
     */
    public function update($force = false)
    {
        if( $this->hasUpdate() AND ($this->isTime() OR $force))
            $this->inject();
    }

    /**
     * Inject data into Wordpress
     *
     * @return void
     */
    public function inject()
    {


        // Inject plugin data to Wordpress
        add_filter('plugins_api', function($api, $action, $args)
        {
            if( $action === 'plugin_information' AND $this->slug === $args->slug)
                return $api = $this->formatData();

            return $api;
        }, 5, 3);

        // Inject plugin path to Wordpress
        add_filter('pre_set_site_transient_update_plugins', function($value)
        {
            if ( !empty($value->checked) AND isset($this->plugins[$this->slug]))
                $value->response[$this->path] = $this->plugins[$this->slug];

            return $value;
        }, 1000);
    }

    /**
     * Validate the configuration array
     * @param  array $config
     * @return mixed
     */
    protected function validateConfig(array $config)
    {
        if( !isset($config['slug']) OR !isset($config['path']) OR !isset($config['version']))
        {
            throw new InvalidArgumentException("Your configuration array needs to have slug, path and version set.");
        }

        return true;
    }

    /**
     * Get package info from repositoty
     * @return array
     */
    protected function getPackage()
    {
        if( $this->package)
        {
            return $this->package;
        }

        $this->package = $this->repo->get($this->slug);

        return $this->package;
    }

    /**
     * Format plugin data for Wordpress
     *
     * @return object
     */
    protected function formatData()
    {
        $data = [
            'name'           => '',
            'slug'           => '',
            'version'        => '',
            'author'         => '',
            'author_profile' => '',
            'tested'         => '',
            'last_updated'   => '',
            'homepage'       => '',
            'sections'       =>
            [
                'description' => '',
                'installtion' => '',
                'changelog'   => '',
                'FAQ'         => ''
            ],
            'download_link'  => ''
        ];

        // Merge config and package data and update necessary fields
        $package = array_merge($this->config, $this->package);
        foreach($package as $key => $value)
        {
            if( $key === 'sections' AND is_array($package[$key]))
            {
                foreach($package[$key] as $tab => $content)
                {
                    if( isset($data['sections'][$tab]))
                        $data['sections'][$tab] = $content;
                }
            }

            else if( isset($data[$key]) AND $key !== 'sections')
            {
                $data[$key] = $value;
            }
        }

        // Return data for Wordpress
        return (object) $data;
    }

    /**
     * Determine if it's time to check for updates
     *
     * @return boolean
     */
    protected function isTime()
    {
        $interval = $this->time ? ($this->time * 60 * 60) : 43200;

        if( (time() - $last_update) > $interval)
            return true;

        return false;
    }

    /**
     * Get plugin options
     *
     * @return object
     */
    protected function getOptions()
    {
        if ( is_multisite() )
            $options = get_site_option('plugin_data_'.$this->slug, false, false);
        else
            $options = get_option('plugin_data_'.$this->slug);

        return ($options && isset($options->last_updated) ? $options : $this->setUp());
    }

    /**
     * Set plugin options
     * @param mixed $data
     */
    protected function setOptions($data)
    {
        if( is_array($data) )
            $data = (object) $data;

        if ( is_multisite() )
            update_site_option('plugin_data_'.$this->slug, $data);
        else
            update_option('plugin_data_'.$this->slug, $data);

    }

    /**
     * Setup plugin data options.
     *
     * @return object
     */
    protected function setUp()
    {
        $data = (object) [
            'last_updated'    => time(),
            'current_version' => $this->version,
            'created'         => time()
        ];

        $this->setOptions($data);

        return $data;
    }

    /**
     * Get property
     *
     * @param  string $property
     * @return mixed
     */
    public function __get($property)
    {
        if( $property === 'config' )
            return $this->config;

        return isset($this->config[$property]) ? $this->config[$property] : null;
    }

    /**
     * Disable setting properties on the
     * Updater obejct
     *
     * @param string $property
     * @param string $value
     */
    public function __set($property, $value)
    {
        $this->config[$property] = $value;
    }

    /**
     * Dusable unsetting properties on the
     * Updater object
     *
     * @param string $property
     */
    public function __unset($property)
    {
        return null;
    }

    /**
     * Test if property isset
     *
     * @param  string  $property
     * @return boolean
     */
    public function __isset($property)
    {
        return isset($this->config[$property]);
    }
}