<?php
/**
 * core/handlers/base.php
 *
 * NeechyHandler base class.
 *
 */
require_once('../console/tasks/base.php');
require_once('../core/neechy/constants.php');


class VersionTask extends NeechyTask {

    #
    # Public Methods
    #
    public function run() {
        $format = <<<STDOUT
Neechy Version: %s
For more information, visit %s
STDOUT;

        return sprintf($format, NEECHY_VERSION, NEECHY_URL);
    }
}
