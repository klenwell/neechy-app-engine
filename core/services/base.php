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
    public $type = 'base';

    #
    # Constructor
    #
    public function __construct($config) {
        $this->config = $config;
    }

    #
    # Public Methods
    #
    public function serve() {
    }

    public function serve_error() {
    }

    public function is($type) {
        return $this->type === $type;
    }
}
