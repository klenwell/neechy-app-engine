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


class PreferencesException extends Exception {}


class ChangePasswordValidator extends NeechyValidator {
    #
    # Properties
    #
    # TODO: DRY this and Auth validator.
    const RE_VALID_USERNAME = "/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/";
    const MIN_USERNAME_LENGTH = 6;
    const MIN_PASSWORD_LENGTH = 8;

    #
    # Public Methods
    #
    public function successful() {
        $user = User::find_by_name(User::logged_in('name'));

        try {
            $this->authenticate_user_password($user);
            $this->validate_new_password(
                $user,
                $this->request->post('new-password', ''),
                $this->request->post('new-password-confirm', '')
            );
            return (! $this->has_errors());
        }
        catch (PreferencesException $e) {
            return FALSE;
        }
    }

    #
    # Private Methods
    #
    private function authenticate_user_password($user) {
        $form_key = 'old-password';
        $value = $this->request->post($form_key, '');

        # Rules
        if ( $this->string_is_empty($value) ) {
            $message = 'Enter your password';
            $this->add_error($form_key, $message);
            return FALSE;
        }

        if ( NeechySecurity::verify_password($value, $user->field('password')) ) {
            return TRUE;
        }
        else {
            $message = 'The password you entered is incorrect.';
            $this->add_error($form_key, $message);
            return FALSE;
        }
    }

    private function validate_new_password($user) {
        $new_password_key = 'new-password';
        $password_value = $this->request->post($new_password_key, '');
        $password_confirm_key = 'new-password-confirm';
        $confirm_value = $this->request->post($password_confirm_key, '');

        if ( $this->string_is_empty($password_value) ) {
            $message = 'Password required';
            $this->add_error($new_password_key, $message);
            return FALSE;
        }

        if ( $this->string_is_too_short($password_value, self::MIN_PASSWORD_LENGTH) ) {
            $message = sprintf('Password too short: must be at least %d chars',
                self::MIN_PASSWORD_LENGTH);
            $this->add_error($new_password_key, $message);
            return FALSE;
        }

        if ( ! $this->values_match($password_value, $confirm_value) ) {
            $message = 'Password fields do not match. Please try again.';
            $this->add_error($password_confirm_key, $message);
            return FALSE;
        }

        if ( $this->values_match($password_value, $user->field('name')) ) {
            $message = 'User name and password should not match';
            $this->add_error($new_password_key, $message);
            return FALSE;
        }

        return TRUE;
    }
}
