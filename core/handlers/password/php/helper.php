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
    public function password_group($name, $placeholder, $autofocus, $validator=null) {
      if ( $validator ) {
        $errors = ( isset($validator->errors[$name]) ) ? $validator->errors[$name] : array();
        $value = $validator->field_value($name);
      }
      else {
        $errors = array();
        $value = null;
      }

      $attrs = array(
        'placeholder' => $placeholder
      );

      if ( $autofocus ) {
        $attrs['autofocus'] = null;
      }

      return $this->form_group(
        $this->password_field($name, $value, $attrs),
        $errors
      );
    }


    #
    # Private Methods
    #
}
