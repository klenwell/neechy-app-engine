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

    public $value = null;
    public $user = null;

    #
    # Constructor
    #
    public function __construct($value, $user=null) {
        $this->value = $value;
        $this->user = $user;
    }

    #
    # Public Methods
    #
    public function validate() {
        $this->validate_present();
        $this->validate_min_length();

        if ( $this->user ) {
            $this->validate_not_user_name();
        }

        return $this->has_errors();
    }

    public function validate_present() {
        $key = 'present';
        $message = 'Password required.';

        if ( $this->string_is_empty($this->value) ) {
            $this->add_error($key, $message);
            return false;
        }
        else {
            return true;
        }
    }

    public function validate_min_length() {
        $key = 'min_length';
        $message = sprintf('Password too short: must be at least %d chars.',
                           self::MIN_PASSWORD_LENGTH);

        if ( $this->string_is_too_short($this->value, self::MIN_PASSWORD_LENGTH) ) {
            $this->add_error($key, $message);
            return false;
        }
        else {
            return true;
        }
    }

    public function validate_not_user_name() {
        $key = 'not_user_name';
        $user_name = $this->user->field('name');
        $message = 'User name and password should not match.';

        if ( $this->values_match($this->value, $user_name) ) {
            $this->add_error($key, $message);
            return false;
        }
        else {
            return true;
        }
    }

    public function authenticate_user_password() {
        $key = 'authenticate';
        $message = 'Password is incorrect. Please try again.';

        if ( ! NeechySecurity::verify_password($this->value,
                                               $this->user->field('password')) ) {
            $this->add_error('authenticate', $message);
            return false;
        }
        else {
            return true;
        }
    }
}
