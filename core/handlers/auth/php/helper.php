<?php
require_once('../public/themes/bootstrap/php/helper.php');


class AuthHelper extends BootstrapHelper {
    #
    # Helper function
    #
    function auth_field($field, $attrs, $t, $helper) {
        $type = $attrs[0];
        $placeholder = $attrs[1];
        $autofocus = isset($attrs[2]) ? $attrs[2] : false;

        $value = ( $type == 'password' ) ? NULL : $t->data($field);
        $options = array(
            'class' => 'form-control',
            'placeholder' => $placeholder,
            'required' => null
        );
        if ( $autofocus ) {
            $options['autofocus'] = null;
        }
        $html = $helper->input_field($type, $field, $value, $options);

        # Apply error styling if appropriate
        if ( isset($validation_errors[$field]) ) {
            $html = apply_field_state('error', $html);
        }

        return $html;
    }

    function apply_field_state($state, $inner_html) {
        $format = <<<HTML5
<div class="form-group %s">
  %s
</div>
HTML5;
        $state_class = sprintf('has-%s', $state);
        return sprintf($format, $state_class, $inner_html);
    }
}
