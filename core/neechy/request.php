<?php
/**
 * core/neechy/request.php
 *
 * Neechy Request class.
 *
 */
require_once('../core/neechy/constants.php');
require_once('../core/neechy/errors.php');


class NeechyRequestError extends NeechyError {}


class NeechyRequest {
    #
    # Properties
    #
    static private $instance = null;

    public $handler = null;
    public $action = null;
    public $format = null;

    private $url_params = array();       # array of path params parsed from URI
    private $params = array();           # array of URL params following handler/action
    private $query_params = array();     # associative array of query string params
    private $valid_formats = array('html', 'ajax');

    #
    # Constructor
    #
    public function __construct() {
        $this->url_params = $this->parse_url();
        $this->query_params = array_merge($_GET, $_POST);
        $this->format = $this->set_format();
        $this->handler = (count($this->url_params) > 0) ? $this->url_params[0] : DEFAULT_HANDLER;
        $this->action = (count($this->url_params) > 1) ? $this->url_params[1] : null;

        if ( count($this->url_params) > 2 ) {
            $this->params = array_slice($this->url_params, 2);
        }
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
    public function param($index, $default=null) {
        return (isset($this->params[$index])) ? trim($this->params[$index]) : $default;
    }

    public function query($key, $default=null) {
        return (isset($this->query_params[$key])) ? trim($this->query_params[$key]) : $default;
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
    private function parse_url() {
        if ( ! isset($_SERVER["REQUEST_URI"]) ) {
            throw new NeechyRequestError('$_SERVER["REQUEST_URI"] not found.', 500);
        }

        $url = $_SERVER["REQUEST_URI"];

        if ( substr_count($url, '?') > 0 ) {
            $url_part = explode('?', $url);
            $url = $url_part[0];
        }

        if ( substr_count($url, '/') < 1 ) {
            return array();
        }
        else {
            $url_params = explode('/', $url);
            $url_params = array_slice($url_params, 1);

            $last_index = count($url_params) - 1;
            if ( trim($url_params[$last_index]) == '' ) {
                unset($url_params[$last_index]);
            }

            return $url_params;
        }
    }

    private function set_format() {
        $format = $this->param('format');
        return (in_array($format, $this->valid_formats)) ? $format : 'html';
    }
}
