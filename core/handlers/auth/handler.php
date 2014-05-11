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
                $content = 'in dev';
                #NeechyResponse::redirect($user->url());
            }
            else {
                $this->t->data('errors', $this->errors);
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
        var_dump('register_new_user and redirect now');
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
        $signup_name = $this->request->post('signup-name', '');

        # not empty
        if ( strlen($signup_name) < 0 ) {
            $message = 'User name required';
            $this->add_validation_error('signup-name', $message);
        }

        # valid format
        # based on http://stackoverflow.com/a/1330703/1093087
        $re_valid_username = "/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/";
        $is_valid_format = preg_match($re_valid_username, $signup_name);
        if ( ! $is_valid_format ) {
            $message = 'Invalid format: please use something like neechy' .
                'neechy_user or NeechyUser';
            $this->add_validation_error('signup-name', $message);
        }

        # not used by another user
        $user = User::find_by_name($signup_name);
        if ( ! $user->is_new() ) {
            $message = 'This user name is not available. Please choose another.';
            $this->add_validation_error('signup-name', $message);
        }

        # not user by a page
        $page = Page::find_by_tag($signup_name);
        if ( ! $page->is_new() ) {
            $message = 'This user name is not available. Please choose another.';
            $this->add_validation_error('signup-name', $message);
        }

        return (isset($this->errors['signup-name'])) ?
            count($this->errors['signup-name']) : 0;
    }

    private function validate_registration_email() {
    }

    private function validate_registration_passwords() {
    }

    private function validate_registration_composite() {
    }
}
