<?php
/**
 * core/services/base.php
 *
 * NeechyService base class.
 *
 */
require_once('../core/neechy/constants.php');
require_once('../core/neechy/config.php');


class NeechyService {
    #
    # Properties
    #
    public $service = 'base';

    #
    # Constructor
    #
    public function __construct($conf_path=NULL) {
        NeechyConfig::init($conf_path);
    }

    #
    # Public Methods
    #
    public function serve() {
    }

    public function serve_error() {
    }

    public function is($service) {
        return $this->service === $service;
    }
}
