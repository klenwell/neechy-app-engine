<?php
require_once('../public/themes/bootstrap/php/helper.php');


class PasswordHelper extends BootstrapHelper {

    #
    # Properties
    #

    #
    # Constructor
    #

    #
    # Public Methods
    #
    public function password_group($name, $placeholder, $autofocus, $t) {
      $validation_errors = $t->data('validation-errors');
      $errors = ( isset($validation_errors[$name]) ) ? $validation_errors[$name] : null;

      $attrs = array(
        'class' => 'form-control',
        'placeholder' => $placeholder
      );

      if ( $autofocus ) {
        $attrs['autofocus'] = NULL;
      }

      return $this->bootstrap_form_group(
        $t->password_field($name, null, $attrs),
        $errors
      );
    }


    #
    # Private Methods
    #
}
