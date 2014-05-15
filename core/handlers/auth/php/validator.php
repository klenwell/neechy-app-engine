<?php
/**
 * core/neechy/validator.php
 *
 * Base Neechy Validator class
 *
 */
require_once('../core/neechy/validator.php');
require_once('../core/models/user.php');
require_once('../core/models/page.php');


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
        $this->validate_signup_user();
        $this->validate_signup_email();
        $this->validate_signup_password();
        return (! $this->has_errors());
    }

    #
    # Private Methods
    #
    private function validate_signup_user() {
        $form_key = 'signup-name';
        $value = $this->request->post($form_key, '');

        # Rules
        if ( $this->string_is_empty($value) ) {
            $message = 'User name required';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        if ( $this->string_is_too_short($value, self::MIN_USERNAME_LENGTH) ) {
            $message = sprintf('User name too short: must be at least %d chars',
                self::MIN_USERNAME_LENGTH);
            $this->add_error($form_key, $message);
            return FALSE;
        }

        if ( ! preg_match(self::RE_VALID_USERNAME, $value) ) {
            $message = 'Invalid format: please use something like neechy' .
                'neechy_user or NeechyUser';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        # Name used by another user/page
        $user = User::find_by_name($value);
        if ( ! $user->is_new() ) {
            $message = 'This user name is not available. Please choose another.';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        $page = Page::find_by_tag($value);
        if ( ! $page->is_new() ) {
            $message = 'This user name is not available. Please choose another.';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        return TRUE;
    }

    private function validate_signup_email() {
        $form_key = 'signup-email';
        $value = $this->request->post($form_key, '');

        # Rules
        if ( $this->string_is_empty($value) ) {
            $message = 'Email required';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        if ( ! $this->is_valid_email($value) ) {
            $message = 'Invalid email address';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        return TRUE;
    }

    private function validate_signup_password() {
        $form_key = 'signup-pass';
        $confirm_key = 'signup-pass-confirm';
        $value = $this->request->post($form_key, '');
        $confirm_value = $this->request->post($confirm_key, '');

        # Rules
        if ( $this->string_is_empty($value) ) {
            $message = 'Password required';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        if ( $this->string_is_too_short($value, self::MIN_PASSWORD_LENGTH) ) {
            $message = sprintf('Password too short: must be at least %d chars',
                self::MIN_PASSWORD_LENGTH);
            $this->add_error($form_key, $message);
            return FALSE;
        }

        if ( ! $this->values_match($value, $confirm_value) ) {
            $message = 'Password fields do not match. Please try again.';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        $signup_name = $this->request->post('signup-name', '');
        if ( $this->values_match($value, $signup_name) ) {
            $message = 'User name and password should not match';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        return TRUE;
    }
}
