<?php
/**
 * core/handlers/base.php
 *
 * NeechyHandler base class.
 *
 */
require_once('../core/neechy/constants.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/errors.php');



class NeechyTask {
    #
    # Properties
    #
    public $service = null;

    #
    # Constructor
    #
    public function __construct($service=null) {
        $this->service = $service;
    }

    #
    # Public Methods
    #
    public function run() {
        throw new NeechyError('NeechyHandler::handler should be overridden');
    }

    #
    # Protected Methods
    #
}
