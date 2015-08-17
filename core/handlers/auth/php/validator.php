<?php
/**
 * core/neechy/validator.php
 *
 * Base Neechy Validator class
 *
 */
require_once('../core/neechy/validator.php');
require_once('../core/neechy/security.php');
require_once('../core/models/user.php');
require_once('../core/models/page.php');


class LoginException extends Exception {}


class LoginValidator extends NeechyValidator {
    #
    # Properties
    #
    const FAILURE_MESSAGE = 'The user name or password you entered is incorrect.';

    public $user = NULL;

    #
    # Constructor
    #
    public function __construct($request) {
        $this->request = $request;
    }

    #
    # Public Methods
    #
    public function successful() {
        try {
            $this->authenticate_user_name();
            $this->authenticate_user_password();
            return true;
        }
        catch (LoginException $e) {
            return false;
        }
    }

    #
    # Private Methods
    #
    private function authenticate_user_name() {
        $form_key = 'login-name';
        $value = $this->request->post($form_key, '');

        # Rules
        if ( $this->string_is_empty($value) ) {
            $message = 'Enter your user name';
            $this->add_error($form_key, $message);
            throw new LoginException($message);
        }

        $user = User::find_by_name($value);
        if ( $user->exists() ) {
            $this->user = $user;
            return TRUE;
        }
        else {
            $this->add_error($form_key, self::FAILURE_MESSAGE);
            throw new LoginException(self::FAILURE_MESSAGE);
        }
    }

    private function authenticate_user_password() {
        $form_key = 'login-pass';
        $value = $this->request->post($form_key, '');

        # Rules
        if ( $this->string_is_empty($value) ) {
            $message = 'Enter your password';
            $this->add_error($form_key, $message);
            throw new LoginException($message);
        }

        if ( NeechySecurity::verify_password($value, $this->user->field('password')) ) {
            return TRUE;
        }
        else {
            $this->add_error($form_key, self::FAILURE_MESSAGE);
            throw new LoginException(self::FAILURE_MESSAGE);
        }
    }
}

class SignUpValidator extends NeechyValidator {

    #
    # Properties
    #
    # Based on http://stackoverflow.com/a/1330703/1093087
    const RE_VALID_USERNAME = "/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/";
    const MIN_USERNAME_LENGTH = 6;
    const MIN_PASSWORD_LENGTH = 8;

    #
    # Public Methods
    #
    public function is_valid() {
        $this->validate_signup_user(
            $this->request->post('signup-name', ''),
            'signup-name');
        $this->validate_signup_email(
            $this->request->post('signup-email', ''),
            'signup-email');
        $this->validate_signup_password(
            $this->request->post('signup-pass', ''),
            $this->request->post('signup-pass-confirm', ''),
            $this->request->post('signup-name', ''),
            'signup-pass');
        return (! $this->has_errors());
    }

    public function validate_signup_user($value, $error_key='base') {
        if ( $this->string_is_empty($value) ) {
            $message = 'User name required';
            $this->add_error($error_key, $message);
            return FALSE;
        }

        if ( $this->string_is_too_short($value, self::MIN_USERNAME_LENGTH) ) {
            $message = sprintf('User name too short: must be at least %d chars',
                self::MIN_USERNAME_LENGTH);
            $this->add_error($error_key, $message);
            return FALSE;
        }

        if ( ! preg_match(self::RE_VALID_USERNAME, $value) ) {
            $message = 'Invalid format: please use something like neechy, ' .
                'neechy_user, or NeechyUser';
            $this->add_error($error_key, $message);
            return FALSE;
        }

        # Name used by another user/page
        $user = User::find_by_name($value);
        if ( $user->exists() ) {
            $message = 'This user name is not available. Please choose another.';
            $this->add_error($error_key, $message);
            return FALSE;
        }

        $page = Page::find_by_title($value);
        if ( ! $page->is_new() ) {
            $message = 'This user name is not available. Please choose another.';
            $this->add_error($error_key, $message);
            return FALSE;
        }

        return TRUE;
    }

    public function validate_signup_email($value, $error_key='base') {
        if ( $this->string_is_empty($value) ) {
            $message = 'Email required';
            $this->add_error($error_key, $message);
            return FALSE;
        }

        if ( ! $this->is_valid_email($value) ) {
            $message = 'Invalid email address format';
            $this->add_error($error_key, $message);
            return FALSE;
        }

        return TRUE;
    }

    #
    # Private Methods
    #
    private function validate_signup_password($value, $confirm_value, $user_name,
        $error_key='base') {
        if ( $this->string_is_empty($value) ) {
            $message = 'Password required';
            $this->add_error($error_key, $message);
            return FALSE;
        }

        if ( $this->string_is_too_short($value, self::MIN_PASSWORD_LENGTH) ) {
            $message = sprintf('Password too short: must be at least %d chars',
                self::MIN_PASSWORD_LENGTH);
            $this->add_error($error_key, $message);
            return FALSE;
        }

        if ( ! $this->values_match($value, $confirm_value) ) {
            $message = 'Password fields do not match. Please try again.';
            $this->add_error($error_key, $message);
            return FALSE;
        }

        if ( $this->values_match($value, $user_name) ) {
            $message = 'User name and password should not match';
            $this->add_error($error_key, $message);
            return FALSE;
        }

        return TRUE;
    }
}
