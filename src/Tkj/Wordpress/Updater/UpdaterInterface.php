<?php namespace Tkj\Wordpress\Updater;

interface UpdaterInterface {

    public function hasUpdate();

    public function inject();
}