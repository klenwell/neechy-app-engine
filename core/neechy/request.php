<?php
/**
 * core/neechy/request.php
 *
 * Neechy Request class.
 *
 */


class NeechyRequest {
    #
    # Constants
    #
    const DEFAULT_HANDLER = 'page';

    #
    # Properties
    #
    public $page = NULL;
    public $handler = NULL;
    public $action = NULL;
    public $mod_rewrite_on = FALSE;

    private $params = array();

    #
    # Constructor
    #
    public function __construct() {
        $this->params = array_merge($_GET, $_POST);
        $this->page = $this->param('page', 'Home');
        $this->handler = $this->set_handler();
        $this->action = $this->set_action();
        $this->mod_rewrite_on = array_key_exists('HTTP_MOD_REWRITE', $_SERVER);
    }

    #
    # Public Methods
    #
    public function is($handler, $page=NULL) {
        if ( $page ) {
            return ($this->handler == strtolower($handler)) &&
                ($this->page == $page);
        }
        else {
            return ($this->handler == strtolower($handler));
        }
    }

    public function param($key, $default=NULL) {
        return (isset($this->params[$key])) ? $this->params[$key] : $default;
    }

    public function post($key, $default=NULL) {
        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }

    public function get($key, $default=NULL) {
        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }


    #
    # Private Methods
    #
    private function set_handler() {
        $handler = $this->param('handler');
        return (! is_null($handler)) ? strtolower($handler) : self::DEFAULT_HANDLER;
    }

    private function set_action() {
        $action = $this->param('action');
        return (! is_null($action)) ? strtolower($action) : NULL;
    }
}
