<?php
/**
 * core/services/web.php
 *
 * Neechy WebService class.
 *
 */
require_once('../core/services/base.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/errors.php');
require_once('../core/neechy/security.php');
require_once('../core/neechy/request.php');
require_once('../core/neechy/templater.php');
require_once('../core/neechy/response.php');
require_once('../core/models/page.php');



class NeechyConsoleService extends NeechyService {
    #
    # Properties
    #
    var $args = array();
    var $action = '';
    var $params = array();

    #
    # Constructor
    #
    public function __construct($config) {
        parent::__construct($config);
        $this->args = array_slice($_SERVER['argv'], 1);
        $this->type = 'console';

        if ( ! isset($this->args[0]) ) {
            throw new NeechyConsoleError(
                'invalid console request: no action (task or handler) provided');
        }
        else {
            $this->action = $this->args[0];
            $this->params = array_slice($this->args, 1);
        }
    }

    #
    # Public Methods
    #
    public function serve() {
        try {
            if ( $this->is_task() ) {
                $response = $this->dispatch_to_task();
            }
            elseif ( $this->is_handler() ) {
                $response = $this->dispatch_to_handler();
            }
            else {
                throw new NeechyConsoleError(
                    'invalid console request: no task or handler found');
            }
        }
        catch (NeechyError $e) {
            $response = $this->dispatch_to_error($e);
        }

        $response->to_console();
    }

    #
    # Private Functions
    #
    private function is_task() {
        return ! is_null($this->task_path());
    }

    private function is_handler() {
        return ! is_null($this->handler_path());
    }

    private function dispatch_to_task() {
        $task = $this->load_task();
        $output = $task->run();
        $response = new NeechyResponse($output);
        return $response;
    }

    private function dispatch_to_handler() {
        $handler = $this->load_handler();
        $content = $handler->handle();
        $response = new NeechyResponse($content);
        return $response;
    }

    private function dispatch_to_error($e) {
        $format = <<<STDERR

NEECHY CONSOLE ERROR:
%s


STDERR;
        $output = sprintf($format, $e->getMessage());
        $response = NeechyResponse::stderr($output);
        return $response;
    }

    private function task_path() {
        $task_name = $this->action;
        $task_app_path = NeechyPath::join(NEECHY_TASK_APP_PATH, $task_name, 'task.php');
        $task_console_path = NeechyPath::join(
            NEECHY_TASK_CONSOLE_PATH, $task_name, 'task.php');

        if ( file_exists($task_app_path) ) {
            return $task_app_path;
        }
        elseif ( file_exists($task_console_path) ) {
            return $task_console_path;
        }
        else {
            return NULL;
        }
    }

    private function handler_path() {
        $handler_name = $this->action;
        $handler_app_path = NeechyPath::join(NEECHY_HANDLER_APP_PATH,
            $handler_name, 'handler.php');
        $handler_core_path = NeechyPath::join(NEECHY_HANDLER_CORE_PATH,
            $handler_name, 'handler.php');

        if ( file_exists($handler_app_path) ) {
            return $handler_app_path;
        }
        elseif ( file_exists($handler_core_path) ) {
            return $handler_core_path;
        }
        else {
            return NULL;
        }
    }

    private function load_task() {
        $task_path = $this->task_path();
        $TaskClass = sprintf('%sTask', ucwords($this->action));

        require_once($task_path);
        $task = new $TaskClass($this);
        return $task;
    }

    private function load_handler() {
        $handler_path = $this->handler_path();
        $HandlerClass = sprintf('%sHandler', ucwords($this->action));

        require_once($handler_path);
        $handler = new $HandlerClass();
        $handler->service = $this;
        $handler->is_console = $this->is('console');
        return $handler;
    }
}
