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

    #
    # Constructor
    #
    public function __construct() {
        NeechyConfig::init();
    }

    #
    # Public Methods
    #
    public function serve() {
    }

    public function serve_error() {
    }
}

