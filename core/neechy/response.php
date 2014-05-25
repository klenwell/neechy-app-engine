<?php
/**
 * core/neechy/response.php
 *
 * Neechy Response class
 *
 */


class NeechyResponse {

    #
    # Properties
    #
    public $status = 0;
    public $body = '';
    public $headers = array();

    #
    # Constructor
    #
    public function __construct($body='', $status=200) {
        $this->body = $body;
        $this->status = $status;
    }

    #
    # Public Static Methods
    #
    static public function redirect($url, $status_code=303) {
        # See http://stackoverflow.com/a/768472/1093087
        $location = sprintf('Location: %s', $url);
        header($location, true, $status_code);
        die();
    }

    static public function stdout($body='**no output**') {
        return new NeechyResponse($body, 'stdout');
    }

    static public function stderr($body) {
        return new NeechyResponse($body, 'stderr');
    }

    #
    # Public Methods
    #
    public function render() {
        print($this->body);
    }

    public function to_console() {
        $stdf = 'php://%s';
        $valid_formats = array('stdout', 'stderr');
        $std_file = 'stdout';

        if ( in_array($this->status, $valid_formats) ) {
            $std_file = $this->status;
        }

        $fh = fopen(sprintf($stdf, $std_file),'w');
        fwrite($fh, sprintf("\n%s\n\n", trim($this->body)));
        fclose($fh);
    }

    public function header($field, $value=NULL) {
        # Sets or returns value for given field.
        #
        # Header field names are case-insensitive.
        # See http://stackoverflow.com/a/5259004/1093087
        $key = strtolower($field);

        if ( is_null($value) ) {
            if ( isset($this->headers[$key]) ) {
                list($key, $value) = explode(':', $this->headers[$key]);
                return $value;
            }
            else {
                return NULL;
            }
        }
        else {
            $this->headers[$key] = sprintf('%s: %s', $field, $value);
            return $this->headers[$key];
        }
    }

    public function send_headers() {
        foreach ($this->headers as $key => $header) {
            header($header);
        }

        if ( $this->status ) {
            $header = sprintf('X-PHP-Response-Code: %d', $this->status);
            header($header, true, $this->status);
        }
    }
}
