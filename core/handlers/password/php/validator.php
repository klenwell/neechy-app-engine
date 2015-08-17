<?php
/**
 * core/handlers/password/php/validator.php
 *
 * Password Handler Validator
 *
 */
require_once('../core/neechy/validator.php');
require_once('../core/neechy/security.php');
require_once('../core/validators/password.php');
require_once('../core/models/user.php');


class PasswordFormValidator extends NeechyValidator {
    #
    # Properties
    #
    public $fields = array();

    #
    # Constructor
    #
    public function __construct($request) {
        $this->request = $request;
    }

    #
    # Public Methods
    #
    public function validate($old_field, $new_field, $confirm_field) {
        $user = User::current();

        $old_password = new PasswordValidator($this->request->post($old_field, ''),
                                              $user);
        $new_password = new PasswordValidator($this->request->post($new_field, ''),
                                              $user);
        $confirmation = new PasswordValidator($this->request->post($confirm_field, ''),
                                              $user);

        $this->fields = array(
            $old_field => $old_password,
            $new_field => $new_password,
            $confirm_field => $confirmation
        );

        $old_password->validate_present();
        $new_password->validate_present();
        $confirmation->validate_present();

        if ( $old_password->is_valid() && $new_password->is_valid() ) {
            $old_password->authenticate_user_password();
            $new_password->validate_min_length();
            $new_password->validate_not_user_name();
            $this->validate_confirmation_match($new_field, $confirm_field);
        }

        if ( $new_password->is_valid() && $confirmation->is_valid() ) {
            $this->set_valid_field_value($new_field, $new_password->value);
            $this->set_valid_field_value($confirm_field, $confirmation->value);
        }

        $this->flatten_validator_errors();

        return $this->is_valid();
    }

    #
    # Private Methods
    #
    private function validate_confirmation_match($field, $confirm_field) {
        $value = $this->request->post($field, '');
        $confirm_value = $this->request->post($confirm_field, '');
        $message = 'Password fields do not match. Please try again.';

        if ( ! $this->values_match($value, $confirm_value) ) {
            $this->add_error($confirm_field, $message);
            return false;
        }
        else {
            return true;
        }
    }

    private function flatten_validator_errors() {
        foreach ( $this->fields as $field => $validator ) {
            if ( ! $validator->is_valid() ) {
                foreach( $validator->errors as $key => $errors ) {
                    foreach ( $errors as $error ) {
                        $this->add_error($field, $error);
                    }
                }
            }
        }
    }
}
