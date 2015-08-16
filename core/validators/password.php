<?php
/**
 * core/handlers/preferences/php/validator.php
 *
 * Preferences Handler Validator
 *
 */
require_once('../core/neechy/validator.php');
require_once('../core/neechy/security.php');
require_once('../core/models/user.php');
require_once('../core/models/page.php');


class PasswordValidator extends NeechyValidator {
    #
    # Properties
    #
    const RE_VALID_USERNAME = "/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/";
    const MIN_USERNAME_LENGTH = 6;
    const MIN_PASSWORD_LENGTH = 8;

    #
    # Public Methods
    #
    public function validate_change($old_field, $new_field, $confirm_field) {
        $old_present = $this->validate_present($old_field);
        $new_present = $this->validate_present($new_field);
        $this->validate_present($confirm_field);

        if ( $old_present && $new_present ) {
            $user = User::current();
            $this->authenticate_user_password($user, $old_field);
            $this->validate_min_length($new_field);
            $this->validate_confirmation_match($new_field, $confirm_field);
            $this->validate_not_user_name($user, $new_field);
        }

        return $this->is_valid();
    }

    #
    # Private Methods
    #
    private function validate_present($field, $message=null) {
        $value = $this->request->post($field, '');
        $message = ( $message ) ? $message : 'Password required.';

        if ( $this->string_is_empty($value) ) {
            $this->add_error($field, $message);
            return false;
        }
        else {
            return true;
        }
    }

    private function authenticate_user_password($user, $field, $message=null) {
        $value = $this->request->post($field, '');
        $default_message = sprintf('Password is incorrect. Please try again.',
                                   self::MIN_PASSWORD_LENGTH);
        $message = ( $message ) ? $message : $default_message;

        if ( ! NeechySecurity::verify_password($value, $user->field('password')) ) {
            $this->add_error($field, $message);
            return false;
        }
        else {
            return true;
        }
    }

    private function validate_min_length($field, $message=null) {
        $value = $this->request->post($field, '');
        $default_message = sprintf('Password too short: must be at least %d chars.',
                                   self::MIN_PASSWORD_LENGTH);
        $message = ( $message ) ? $message : $default_message;

        if ( $this->string_is_too_short($value, self::MIN_PASSWORD_LENGTH) ) {
            $this->add_error($field, $message);
            return false;
        }
        else {
            return true;
        }
    }

    private function validate_confirmation_match($field, $confirm_field, $message=null) {
        $value = $this->request->post($field, '');
        $confirm_value = $this->request->post($confirm_field, '');
        $message = ( $message ) ? $message : 'Password fields do not match. Please try again.';

        if ( ! $this->values_match($value, $confirm_value) ) {
            $this->add_error($confirm_field, $message);
            return false;
        }
        else {
            return true;
        }
    }

    private function validate_not_user_name($user, $field, $message=null) {
        $value = $this->request->post($field, '');
        $user_name = $user->field('name');
        $message = ( $message ) ? $message : 'User name and password should not match.';

        if ( $this->values_match($value, $user_name) ) {
            $this->add_error($field, $message);
            return false;
        }
        else {
            return true;
        }
    }
}
