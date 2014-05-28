<?php namespace Tkj\Wordpress\Updater\Repositories;

interface UpdaterRepositoryInterface {

    /**
     * Get package info
     * @param  string $slug
     * @return array
     */
    public function get($slug);

}