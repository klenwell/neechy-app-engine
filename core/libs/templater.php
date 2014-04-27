<?php
/**
 * core/libs/templater.php
 *
 * Neechy templating engine. Renders final output.
 *
 */
require_once('../core/libs/constants.php');
require_once('../core/libs/utilities.php');


class NeechyTemplater {
    #
    # Constants
    #
    const CORE_LAYOUT_PATH = 'core/templates/layout.html.php';
    const THEME_LAYOUT_PATH = 'html/layout.html.php';

    #
    # Properties
    #
    public $partial = array();
    private $theme_path = '';

    #
    # Constructor
    #
    public function __construct() {
        $this->theme_path = $this->load_theme_path();
    }

    #
    # Public Methods
    #
    public function render() {
        $output = '';
        $layout = $this->load_layout();
        $partial_tokens = $this->extract_partial_tokens($layout);

        foreach ( $partial_tokens as $token ) {
            $content = $this->render_partial($token);
            $layout = str_replace($token, $content, $layout);
        }

        return $layout;
    }

    public function set() {}

    #
    # Private Methods
    #
    private function load_theme_path() {
        $theme = 'bootstrap';
        return NeechyPath::join(NEECHY_PUBLIC_PATH, 'themes', $theme);
    }

    private function load_layout() {
        # First look in theme directory
        $layout_path = NeechyPath::join($this->theme_path, self::THEME_LAYOUT_PATH);
        if ( file_exists($layout_path) ) {
            return $this->buffer($layout_path);
        }

        # If not found there, default to core layout
        $layout_path = NeechyPath::join(NEECHY_ROOT, self::CORE_LAYOUT_PATH);
        return $this->buffer($layout_path);
    }

    private function extract_partial_tokens($layout) {
        $partial_tokens = array();
        $regex = '/\{\{\s*[^\}]+\}\}/';

        $matched = preg_match_all($regex, $layout, $partial_tokens);

        if ( $matched ) {
            return $partial_tokens[0];
        }
        else {
            return array();
        }
    }

    private function render_partial($token) {
        $id = preg_replace('/[\{\}\s]/', '', $token);

        if ( method_exists($this, $id) ) {
            return $this->$id();
        }
        elseif ( isset($this->partial[$id]) ) {
            return $this->partial[$id];
        }
        else {
            return sprintf('!-- block %s not found -->', $id);
        }
    }

    private function buffer($path) {
        ob_start();
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}
