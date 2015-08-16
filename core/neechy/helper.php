<?php
/**
 * core/neechy/helper.php
 *
 * Base Neechy Helper class
 *
 */


class NeechyHelper {

    #
    # Properties
    #
    public $request = null;

    #
    # Constructor
    #
    public function __construct($request=NULL) {
        $this->request = $request;
    }

    #
    # Public Methods
    #
    public function input_field($type, $name, $value=NULL, $attrs=array()) {
        $format = '<input type="%s" name="%s"%s%s />';

        if ( ! is_null($value) ) {
            $value_attr = sprintf(' value="%s"', str_replace('"', '\"', $value));
        }
        else {
            $value_attr = '';
        }

        $attr_string = $this->array_to_attr_string($attrs);

        return sprintf($format, $type, $name, $value_attr, $attr_string);
    }

    public function open_bootstrap_form($url, $method='POST', $attrs=array(),
                                        $hidden_fields=array()) {
        $format = '<form role="form" method="%s" action="%s"%s />';
        $bootstrap_class = 'form-login';

        if ( isset($attrs['class']) ) {
            $attrs['class'] = sprintf('%s %s', $bootstrap_class, $attrs['class']);
        }
        else {
            $attrs['class'] = $bootstrap_class;
        }

        $attr_string = $this->array_to_attr_string($attrs);
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

    public function bootstrap_submit_button($label, $state='primary', $attrs=array()) {
        $format = '<button type="submit" %s>%s</button>';
        $state_class = sprintf('btn-%s', $state);
        $bootstrap_class = sprintf('btn btn-lg %s btn-block', $state_class);

        if ( isset($attrs['class']) ) {
            $attrs['class'] = sprintf('%s %s', $bootstrap_class, $attrs['class']);
        }
        else {
            $attrs['class'] = $bootstrap_class;
        }

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

    public function neechy_link($label, $handler=null, $page=null, $action=null,
                                $attrs=array()) {
        $page = (is_null($page)) ? $label : $page;
        $href = NeechyPath::url($page, $handler, $action);
        return $this->link($href, $label, $attrs);
    }

    public function link($href, $text, $attrs=array()) {
        $format = '<a %s>%s</a>';
        $tag_attrs = array(sprintf('href="%s"', $href));

        foreach ( $attrs as $attr => $value ) {
            $tag_attrs[] = sprintf('%s="%s"', $attr, $value);
        }

        return sprintf($format, implode(' ', $tag_attrs), $text);
    }

    #
    # Protected Methods
    #
    protected function array_to_attr_string($options) {
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
