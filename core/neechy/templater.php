<?php
/**
 * core/neechy/templater.php
 *
 * Neechy templating engine. Renders final output.
 *
 */
require_once('../core/neechy/constants.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/request.php');


class NeechyTemplater {
    #
    # Constants
    #
    const CORE_LAYOUT_PATH = 'core/templates/layout.html.php';
    const THEME_LAYOUT_PATH = 'html/layout.html.php';

    #
    # Properties
    #
    static private $instance = null;

    public $request = null;
    public $page = null;

    private $_data = array();
    private $partial = array();
    private $theme_path = '';
    private $theme_url_path = '';

    #
    # Constructor
    #
    public function __construct($theme='bootstrap') {
        $this->theme_path = $this->load_theme_path($theme);
        $this->theme_url_path = sprintf('themes/%s/', $theme);
        $this->request = NeechyRequest::load();
    }

    #
    # Static Public Methods
    #
    static public function load($theme='bootstrap') {
        if ( ! is_null(self::$instance) ) {
            return self::$instance;
        }
        else {
            self::$instance = new NeechyTemplater($theme);
            return self::$instance;
        }
    }

    static public function titleize_camel_case($input) {
        # Based on: http://stackoverflow.com/a/1993772/1093087
        $regex = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
        preg_match_all($regex, $input, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
          $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return ucwords(implode(' ', $ret));
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
        #
        # This sets values for partials, values which will replace {{ tokens }}
        # in templates.
        #
        $current_value = $this->render_partial_by_id($id);
        $this->partial[$id] = $value;
        return $current_value;
    }

    public function data($key, $value=NULL) {
        #
        # This method provides a method for setting variables that can be
        # accessed in partials through the templater object. It is comparable
        # to the way the data method functions in jQuery. For an example, see:
        # public/themes/bootstrap/html/editor.html.php
        #
        if ( ! is_null($value) ) {
            $this->_data[$key] = $value;
        }
        else {
            $this->_data[$key] = ( isset($this->_data[$key]) ) ? $this->_data[$key] : '';
        }

        return $this->_data[$key];
    }

    public function link($href, $text, $options=array()) {
        $format = '<a %s>%s</a>';
        $attrs = array(sprintf('href="%s"', $href));

        foreach ( $options as $attr => $value ) {
            $attrs[] = sprintf('%s="%s"', $attr, $value);
        }

        return sprintf($format, implode(' ', $attrs), $text);
    }

    public function neechy_link($label, $page=NULL, $handler=NULL, $action=NULL,
        $options=array()) {

        $page = (is_null($page)) ? $label : $page;
        $href = NeechyPath::url($page, $handler, $action);
        return $this->link($href, $label, $options);
    }

    public function render_editor($input='') {
        $default_path = $this->load_theme_path('bootstrap');
        $default_editor = NeechyPath::join($default_path, 'html/editor.html.php');
        $theme_editor = NeechyPath::join($this->theme_path, 'html/editor.html.php');

        $this->data('editor-content', $input);

        if ( file_exists($theme_editor) ) {
            return $this->buffer($theme_editor);
        }
        else {
            return $this->buffer($default_editor);
        }
    }

    public function js_src($fpath='') {
        return NeechyPath::join($this->theme_url_path, 'js', $fpath);
    }

    public function css_href($fpath='') {
        return NeechyPath::join($this->theme_url_path, 'css', $fpath);
    }

    public function js_link($src) {
        $format = '<script src="%s"></script>';
        return sprintf($format, $src);
    }

    public function css_link($href) {
        $format = '<link rel="stylesheet" href="%s" />';
        return sprintf($format, $href);
    }

    public function append_to_head($markup) {
        $key = 'head_appendix';
        $this->partial[$key] = (isset($this->partial[$key])) ? $this->partial[$key] : '';
        $this->partial[$key] = sprintf("%s\n%s", $this->partial[$key], $markup);
        return $this;
    }

    public function append_to_body($markup) {
        $key = 'body_appendix';
        $this->partial[$key] = (isset($this->partial[$key])) ? $this->partial[$key] : '';
        $this->partial[$key] = sprintf("%s\n%s", $this->partial[$key], $markup);
        return $this;
    }

    public function page_title() {
        if ( ! $this->page ) {
            return '<!-- no page found -->';
        }
        else {
            return self::titleize_camel_case($this->page->field('tag',
                '<!-- no tag found -->'));
        }
    }

    public function nav_tab_class($link_page_tag) {
        if ( strtolower($link_page_tag) == strtolower($this->request->page) ) {
            return 'active';
        }
        else {
            return 'inactive';
        }
    }

    public function render_partial($token, $partial_path=NULL) {
        #
        # Look for a partial file in the given path (if set), else in the
        # conventional theme html folder. If still not found, check to see if
        # the partial item has been set. Else, return "block not found" html comment.
        #
        $id = preg_replace(RE_EXTRACT_BRACKET_TOKEN_ID, '', $token);
        $theme_file = sprintf('%s.html.php', $id);
        $theme_path = NeechyPath::join($this->theme_path, 'html', $theme_file);

        if ( ! is_null($partial_path) ) {
            return $this->buffer($partial_path);
        }
        elseif ( isset($this->partial[$id]) ) {
            return $this->partial[$id];
        }
        elseif ( file_exists($theme_path) ) {
            return $this->buffer($theme_path);
        }
        else {
            return sprintf('<!-- block %s not found -->', $id);
        }
    }

    public function render_partial_by_id($id, $partial_path=NULL) {
        $token = sprintf('{{ %s }}', $id);
        return $this->render_partial($token, $partial_path);
    }

    public function buffer($path) {
        ob_start();
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
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
}
