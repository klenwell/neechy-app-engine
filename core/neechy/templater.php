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
    #
    # Render Methods
    #
    public function render() {
        $output = '';
        $layout = $this->load_layout();
        $partial_tokens = $this->extract_partial_tokens($layout);

        foreach ( $partial_tokens as $token ) {
            $content = $this->render_partial_by_token($token);
            $layout = str_replace($token, $content, $layout);
        }

        return $layout;
    }

    public function buffer($path) {
        ob_start();
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function render_partial_by_path($partial_path) {
        return $this->buffer($partial_path);
    }

    public function render_partial_by_token($token) {
        $id = preg_replace(RE_EXTRACT_BRACKET_TOKEN_ID, '', $token);
        return $this->render_partial_by_id($id);
    }

    public function render_partial_by_id($id) {
        $partial_file = sprintf('%s.html.php', $id);
        $theme_path = NeechyPath::join($this->theme_path, 'html', $partial_file);

        if ( isset($this->partial[$id]) ) {
            return $this->partial[$id];
        }
        elseif ( file_exists($theme_path) ) {
            return $this->render_partial_by_path($theme_path);
        }
        else {
            return sprintf('<!-- block %s not found -->', $id);
        }
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

    #
    # Data Methods
    #
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

    #
    # Helper Methods
    #
    public function flash($message, $class='info') {
        $_SESSION['neechy-flash'] = ( isset($_SESSION['neechy-flash']) ) ?
            $_SESSION['neechy-flash'] : array();

        if ( isset($_SESSION['neechy-flash'][$class]) ) {
            $_SESSION['neechy-flash'][$class][] = $message;
        }
        else {
            $_SESSION['neechy-flash'][$class] = array($message);
        }

        return null;
    }

    public function has_flash() {
        return isset($_SESSION['neechy-flash']);
    }

    public function unflash() {
        $alerts = array();
        $alert_format = <<<HTML5
    <div class="alert alert-%s alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
            &times;
        </button>
        %s
    </div>
HTML5;
        $format = <<<HTML5
<div id="neechy-flash-alerts">
%s
</div>
HTML5;

        if ( ! $this->has_flash() ) {
            return '<!-- no flash messages -->';
        }

        foreach ( $_SESSION['neechy-flash'] as $class => $messages ) {
            foreach ( $messages as $message ) {
                $alerts[] = sprintf($alert_format, $class, $message);
            }
            unset($_SESSION['neechy-flash'][$class]);
        }

        unset($_SESSION['neechy-flash']);
        return sprintf($format, implode("\n", $alerts));
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

    public function nav_tab_class($link_page_tag) {
        if ( strtolower($link_page_tag) == strtolower($this->request->page) ) {
            return 'active';
        }
        else {
            return 'inactive';
        }
    }

    #
    # Form Helper Methods
    #
    public function open_form($url, $method='POST', $options=array(),
                              $hidden_fields=array()) {
        $format = '<form role="form" method="%s" action="%s"%s />';
        $attr_string = $this->array_to_attr_string($options);
        $form_tag = sprintf($format, $method, $url, $attr_string);

        # Add CSRF token for POST forms
        if (strtoupper($method) == 'POST') {
            $hidden_fields['csrf_token'] = $_SESSION['csrf_token'];
        }

        $hidden_tags = array();
        foreach ( $hidden_fields as $field => $value ) {
            $hidden_tags[] = $this->input_field('hidden', $field, $value);
        }

        if ( $hidden_tags ) {
            $hidden_tag_list = implode("\n", $hidden_tags);
            $form_tag = implode("\n", array($form_tag, $hidden_tag_list));
        }

        return $form_tag;
    }

    public function close_form($action='') {
        # $action will add a hidden field with action value.
        $format = "%s\n</form>";
        $hidden_field = '';

        if ( $action ) {
            $hidden_field = $this->input_field('hidden', 'action', $action);
        }

        return sprintf($format, $hidden_field);
    }

    public function input_field($type, $name, $value=NULL, $options=array()) {
        $format = '<input type="%s" name="%s"%s%s />';

        if ( ! is_null($value) ) {
            $value_attr = sprintf(' value="%s"', str_replace('"', '\"', $value));
        }
        else {
            $value_attr = '';
        }

        $optional_attrs = $this->array_to_attr_string($options);

        return sprintf($format, $type, $name, $value_attr, $optional_attrs);
    }

    public function password_field($name, $value=NULL, $options=array()) {
        return $this->input_field('password', $name, $value, $options);
    }

    public function submit_button($label, $attrs=array()) {
        $format = '<button type="submit" %s>%s</button>';
        $optional_attrs = $this->array_to_attr_string($attrs);
        return sprintf($format, $optional_attrs, $label);
    }

    public function bootstrap_form_group($inner_html, $errors=array()) {
        # Set group class
        $validation_class = ( $errors ) ? 'has-error' : '';
        $group_class = sprintf('form-group %s', $validation_class);

        # Add feedback for errors
        $help_spans = array();

        if ( $errors ) {
            foreach ( $errors as $message ) {
                $help_spans[] = sprintf('<span class="help-block">%s</span>', $message);
            }
        }

        $format = <<<HTML5
<div class="%s">
  %s
  %s
</div>
HTML5;

        return sprintf($format, $group_class, $inner_html, join("\n", $help_spans));
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

    private function array_to_attr_string($options) {
        $attr_list = array();

        foreach( $options as $attr => $val ) {
            if ( is_null($val) ) {
                $attr_list[] = $attr;
            }
            else {
                $attr_list[] = sprintf(' %s="%s"',
                    $attr,
                    str_replace('"', '\"', $val));
            }
        }

        if ($attr_list) {
            $attr_string = ' ' . implode(' ', $attr_list);
        }
        else {
            $attr_string = '';
        }

        return $attr_string;
    }
}
