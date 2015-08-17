<?php
/**
 * core/handlers/password/handler.php
 *
 * PasswordHandler class.
 *
 * Console (CLI): See help method below.
 *
 */
require_once('../core/handlers/base.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/templater.php');
require_once('../core/handlers/password/php/validator.php');


class PasswordHandler extends NeechyHandler {

    #
    # Public Methods
    #
    public function handle() {
        #
        if ( $this->is_console ) {
            $this->params = $this->service->params;
            return $this->console();
        }

        # If not logged in, redirect to login page
        if ( ! User::is_logged_in() ) {
            $this->t->flash('Please login to access that page.', 'warning');
            return $this->redirect('auth', 'login');
        }

        # Change password request
        if ( $this->request->action_is('change-password') ) {
            $form = new PasswordFormValidator($this->request);

            if ( $form->validate('old-password', 'new-password', 'new-password-confirm') ) {
                $user = User::current();
                $user->set_password($this->request->post('new-password'));
                if ( $user->save() ) {
                    $this->t->flash('Your password has been changed.', 'success');
                }
                else {
                    $this->t->flash('There was a problem saving your password.', 'danger');
                }
            }
            else {
                $this->t->data('form-validator', $form);
            }

            $content = $this->render_view('content');
        }

        # Default: display
        else {
            $content = $this->render_view('content');
        }

        return $content;
    }

    #
    # Private
    #
    private function console() {
        $action = ( count($this->params) > 0 ) ? $this->params[0] : null;

        if ( $action == 'reset' ) {
            $this->confirm_password_reset();
        }
        else {
            $this->help();
        }
    }

    private function confirm_password_reset() {
        $user_name = ( count($this->params) > 1 ) ? $this->params[1] : null;

        if ( ! $user_name ) {
            $this->help();
            $this->print_error('You must provide a user name');
        }

        $user = User::find_by_name($user_name);

        if ( ! $user ) {
            $this->print_error(sprintf('User "%s" not found', $user_name));
        }

        $confirmed = $this->prompt_user(sprintf(
            'Are you sure your want to reset the password for user "%s"? [Y/n] ',
            $user_name)
        );

        if ( $confirmed == 'Y' ) {
            $new_password = $this->reset_user_password($user);
            $stdout = <<<STDOUT
    Password for user %s has been reset to:

    %s
STDOUT;
            printf($stdout, $user_name, $new_password);
        }
        else {
            $this->println('Password will not be reset.');
        }
    }

    private function help() {
        $usage = <<<STDOUT
Usage:
    php console/run.php preferences reset [user]

    Resets given [user] password with automatically generated one.
STDOUT;

        echo $usage;
    }

    private function reset_user_password($user, $password=null) {
        $password = ( ! empty($password) ) ? $password : NeechySecurity::random_hex();
        $user->set_password($password);
        $user->save();
        return $password;
    }
}
