<?php

function password_group($name, $placeholder, $autofocus, $t) {
  $validation_errors = $t->data('validation-errors');
  $errors = ( isset($validation_errors[$name]) ) ? $validation_errors[$name] : null;

  $attrs = array(
    'class' => 'form-control',
    'placeholder' => $placeholder
  );

  if ( $autofocus ) {
    $attrs['autofocus'] = NULL;
  }

  return $t->bootstrap_form_group(
    $t->password_field($name, null, $attrs),
    $errors
  );
}
