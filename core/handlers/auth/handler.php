<?php
/**
 * core/handlers/auth/handler.php
 *
 * PageHandler class.
 *
 */
require_once('../core/handlers/base.php');
require_once('../core/neechy/templater.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/constants.php');
require_once('../core/models/user.php');
require_once('../core/models/page.php');


class AuthHandler extends NeechyHandler {
    #
    # Properties
    #
    public $page = null;

    private $errors = array();

    #
    # Public Methods
    #
    public function handle() {
        if ( $this->request->action_is('login') ) {
            $this->t->data('alert', 'logging in');
            $content = $this->render_view('login');
        }
        elseif ( $this->request->action_is('register') ) {
            if ( $this->registration_is_valid() ) {
                $user = $this->register_new_user();
                NeechyResponse::redirect($user->url());
            }
            else {
                $this->t->data('validation-errors', $this->errors);
                $content = $this->render_view('login');
            }
        }
        else {
            $content = $this->render_view('login');
        }

        return $content;
    }

    #
    # Private
    #
    private function register_new_user() {
        # Save user
        $name = $this->request->post('signup-name');
        $user = User::find_by_name($name);
        $user->set('email', $this->request->post('signup-email'));
        $user->set('password', 'HASH PASSWORD');
        $user->save();

        # Create user page
        $this->t->data('new-user', $user->fields);
        $path = NeechyPath::join($this->html_path(), 'new-page.md.php');
        $content = $this->t->render_partial_by_id(NULL, $path);
        $page = Page::find_by_tag($name);
        $page->set('body', $content);
        $page->set('editor', $name);
        $page->save();

        return $user;
    }

    private function add_validation_error($field, $message) {
        if ( isset($this->errors[$field]) ) {
            $this->errors[$field][] = $message;
        }
        else {
            $this->errors[$field] = array($message);
        }
        return $this->errors;
    }

    #
    # Registration Validators
    #
    private function registration_is_valid() {
        $this->validate_registration_user();
        $this->validate_registration_email();
        $this->validate_registration_passwords();
        $this->validate_registration_composite();
        var_dump($this->errors);
        return count($this->errors) < 1;
    }

    private function validate_registration_user() {
        $form_key = 'signup-name';
        $input_value = $this->request->post($form_key, '');

        # Empty, too short, or invalid format
        # based on http://stackoverflow.com/a/1330703/1093087
        $re_valid_username = "/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/";
        if ( strlen($input_value) < 0 ) {
            $message = 'User name required';
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }
        elseif ( strlen($input_value) < NEECHY_MIN_USERNAME_LENGTH ) {
            $message = sprintf('User name too short: must be at least %d chars',
                NEECHY_MIN_USERNAME_LENGTH);
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }
        elseif ( ! preg_match($re_valid_username, $input_value) ) {
            $message = 'Invalid format: please use something like neechy' .
                'neechy_user or NeechyUser';
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }

        # Name used by another user/page
        $user = User::find_by_name($input_value);
        if ( ! $user->is_new() ) {
            $message = 'This user name is not available. Please choose another.';
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }

        $page = Page::find_by_tag($input_value);
        if ( ! $page->is_new() ) {
            $message = 'This user name is not available. Please choose another.';
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }

        return TRUE;
    }

    private function validate_registration_email() {
        $form_key = 'signup-email';
        $input_value = $this->request->post($form_key, '');

        # Empty or invalid format
        $is_valid_format = (bool) filter_var($input_value, FILTER_VALIDATE_EMAIL);
        if ( strlen($input_value) < 0 ) {
            $message = 'Email required';
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }
        elseif ( ! $is_valid_format ) {
            $message = 'Invalid email address';
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }

        return TRUE;
    }

    private function validate_registration_passwords() {
        $form_key = 'signup-pass';
        $confirm_key = 'signup-pass-confirm';
        $input_value = $this->request->post($form_key, '');
        $confirm_value = $this->request->post($confirm_key, '');

        # Empty or too short
        if ( strlen($input_value) < 0 ) {
            $message = 'Password required';
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }
        elseif ( strlen($input_value) < NEECHY_MIN_PASSWORD_LENGTH ) {
            $message = sprintf('Password too short: must be at least %d chars',
                NEECHY_MIN_PASSWORD_LENGTH);
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }

        # Confirm match
        if ( $input_value != $confirm_value ) {
            $message = 'Password fields do not match. Please try again.';
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }

        # User name and password should not match
        $signup_name = $this->request->post('signup-name', '');
        if ( $input_value == $signup_name ) {
            $message = 'User name and password should not match';
            $this->add_validation_error($form_key, $message);
            return FALSE;
        }

        return TRUE;
    }

    private function validate_registration_composite() {
    }
}
