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
    public function __construct($request=null) {
        $this->request = $request;
    }

    #
    # Static Methods
    #
    public static function link($href, $text, $attrs=array()) {
        $format = '<a %s>%s</a>';
        $tag_attrs = array(sprintf('href="%s"', $href));

        foreach ( $attrs as $attr => $value ) {
            $tag_attrs[] = sprintf('%s="%s"', $attr, $value);
        }

        return sprintf($format, implode(' ', $tag_attrs), $text);
    }

    public static function handler_link($label, $handler=null, $action=null, $attrs=array()) {
        $href = NeechyPath::url($handler, $action);
        return self::link($href, $label, $attrs);
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

    public function close_form($purpose='') {
        # $action will add a hidden field with action value.
        $format = "%s\n</form>";
        $hidden_field = '';

        if ( $purpose ) {
            $hidden_field = $this->input_field('hidden', 'purpose', $purpose);
        }

        return sprintf($format, $hidden_field);
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
