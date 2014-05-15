<?php
/**
 * core/handlers/auth/handler.php
 *
 * PageHandler class.
 *
 */
require_once('../core/handlers/base.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/constants.php');
require_once('../core/neechy/security.php');
require_once('../core/neechy/templater.php');
require_once('../core/models/user.php');
require_once('../core/models/page.php');
require_once('../core/handlers/auth/php/validator.php');


class AuthHandler extends NeechyHandler {

    #
    # Public Methods
    #
    public function handle() {
        # TODO: login user
        if ( $this->request->action_is('login') ) {
            $this->t->data('alert', 'logging in');
            $content = $this->render_view('login');
        }
        elseif ( $this->request->action_is('signup') ) {
            $validator = new SignUpValidator($this->request);
            if ( $validator->is_valid() ) {
                $user = $this->register_new_user();
                NeechyResponse::redirect($user->url());
            }
            else {
                $this->t->data('validation-errors', $validator->errors);
                $this->t->data('signup-name', $this->request->post('signup-name'));
                $this->t->data('signup-email', $this->request->post('signup-email'));
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
        $user->set('password',
            NeechySecurity::hash_password($this->request->post('signup-pass')));
        $user->save();

        # Create user page
        $this->t->data('new-user', $user->fields);
        $path = NeechyPath::join($this->html_path(), 'new-page.md.php');
        $page = Page::find_by_tag($name);
        $page->set('body', $content);
        $page->set('editor', $name);
        $page->save();

        $content = $this->t->render_partial_by_path($path);
        return $user;
    }
}
