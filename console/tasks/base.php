<?php
/**
 * console/tasks/base.php
 *
 * Base Neechy task class.
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
    public $params = array();

    #
    # Constructor
    #
    public function __construct($service=null) {
        $this->service = $service;
        $this->params = $service->params;
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
