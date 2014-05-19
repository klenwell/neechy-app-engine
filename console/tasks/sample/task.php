<?php
/**
 * console/tasks/sample/task.php
 *
 * Sample Neechy task.
 *
 * If you wish to use this as a template, copy to a new directory and changes
 * class name.
 *
 * Usage:
 *  php console/run.php sample one two three
 *
 */
require_once('../console/tasks/base.php');
require_once('../core/neechy/constants.php');


class SampleTask extends NeechyTask {

    #
    # Public Methods
    #
    public function run() {
        $format = <<<STDOUT
A sample Neechy task
To run: php console/run.php sample [params]

Action: %s
Parameters: %s
STDOUT;
        $params = ($this->params) ? implode(', ', $this->params) : '(none)';
        return sprintf($format, $this->service->action, $params);
    }
}
