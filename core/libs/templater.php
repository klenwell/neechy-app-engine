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
    private $partial = array();
    private $theme_path = '';

    #
    # Constructor
    #
    public function __construct($theme='bootstrap') {
        $this->theme_path = $this->load_theme_path($theme);
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

    public function set($id, $value) {
        $current_value = $this->render_partial_by_id($id);
        $this->partial[$id] = $value;
        return $current_value;
    }

    public function link($href, $text, $options=array()) {
        $format = '<a %s>%s</a>';
        $attrs = array(sprintf('href="%s"', $href));

        foreach ( $options as $attr => $value ) {
            $attrs[] = sprintf('%s="%s"', $attr, $value);
        }

        return sprintf($format, implode(' ', $attrs), $text);
    }

    #
    # Private Methods
    #
    private function load_theme_path($theme) {
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

        $matched = preg_match_all(RE_BRACKET_TOKENS, $layout, $partial_tokens);

        if ( $matched ) {
            return $partial_tokens[0];
        }
        else {
            return array();
        }
    }

    private function render_partial($token) {
        #
        # Look for a partial file in the conventional theme html folder. If not
        # found, check to see if the partial item has been set. Else, return
        # "block not found" html comment.
        #
        $id = preg_replace(RE_EXTRACT_BRACKET_TOKEN_ID, '', $token);
        $partial_file = sprintf('%s.html.php', $id);
        $partial_path = NeechyPath::join($this->theme_path, 'html', $partial_file);

        if ( isset($this->partial[$id]) ) {
            return $this->partial[$id];
        }
        elseif ( file_exists($partial_path) ) {
            return $this->buffer($partial_path);
        }
        else {
            return sprintf('<!-- block %s not found -->', $id);
        }
    }

    private function render_partial_by_id($id) {
        $token = sprintf('{{ %s }}', $id);
        return $this->render_partial($token);
    }

    private function buffer($path) {
        ob_start();
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}
