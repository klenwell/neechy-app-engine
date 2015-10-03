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
    const DEFAULT_PAGE = 'home';
    const DEFAULT_HANDLER = 'page';

    #
    # Properties
    #
    static private $instance = null;

    public $page = NULL;
    public $handler = NULL;
    public $action = NULL;
    public $mod_rewrite_on = FALSE;

    private $valid_formats = array('html', 'ajax');
    private $params = array();

    #
    # Constructor
    #
    public function __construct() {
        $this->params = array_merge($_GET, $_POST);
        $this->format = $this->set_format();
        $this->page = $this->set_page();
        $this->handler = $this->set_handler();
        $this->action = $this->set_action();
        $this->mod_rewrite_on = array_key_exists('HTTP_MOD_REWRITE', $_SERVER);
    }

    #
    # Static Public Methods
    #
    static public function load() {
        if (! is_null(self::$instance)) {
            return self::$instance;
        }
        else {
            self::$instance = new NeechyRequest();
            return self::$instance;
        }
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

    public function page_is($value) {
        if ( is_null($this->page) ) {
            return FALSE;
        }
        else {
            return strtolower($this->page) == strtolower($value);
        }
    }

    public function action_is($value) {
        if ( is_null($this->action) ) {
            return FALSE;
        }
        else {
            return $this->action == $value;
        }
    }

    public function param($key, $default=null) {
        return (isset($this->params[$key])) ? trim($this->params[$key]) : $default;
    }

    public function post($key, $default=null) {
        return (isset($_POST[$key])) ? trim($_POST[$key]) : $default;
    }

    public function get($key, $default=null) {
        return (isset($_GET[$key])) ? trim($_GET[$key]) : $default;
    }

    #
    # Private Methods
    #
    private function set_format() {
        $format = $this->param('format');
        return (in_array($format, $this->valid_formats)) ? $format : 'html';
    }

    private function set_page() {
        return $this->param('page', self::DEFAULT_PAGE);
    }

    private function set_handler() {
        $handler = $this->param('handler');
        return (! is_null($handler)) ? strtolower($handler) : self::DEFAULT_HANDLER;
    }

    private function set_action() {
        $action = $this->param('action');
        return (! is_null($action)) ? strtolower($action) : null;
    }
}
