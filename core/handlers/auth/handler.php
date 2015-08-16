<?php
/**
 * core/handlers/auth/handler.php
 *
 * PageHandler class.
 *
 */
require_once('../core/handlers/base.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/templater.php');
require_once('../core/models/user.php');
require_once('../core/models/page.php');
require_once('../core/handlers/auth/php/validator.php');


class AuthHandler extends NeechyHandler {

    #
    # Public Methods
    #
    public function handle() {
        if ( $this->request->action_is('login') ) {
            $login = new LoginValidator($this->request);
            if ( $login->successful() ) {
                $this->t->flash('You have been logged in.', 'success');
                $login->user->login();
                NeechyResponse::redirect($login->user->url());
            }
            else {
                $this->t->data('validation-errors', $login->errors);
                $this->t->data('login-name', $this->request->post('login-name'));
                $content = $this->render_view('login');
            }
            $this->t->data('alert', 'logging in');
        }
        elseif ( $this->request->action_is('signup') ) {
            $validator = new SignUpValidator($this->request);
            if ( $validator->is_valid() ) {
                $user = User::register(
                    $this->request->post('signup-name'),
                    $this->request->post('signup-email'),
                    $this->request->post('signup-pass')
                );
                $page = $this->save_user_page($user);
                NeechyResponse::redirect($page->url());
            }
            else {
                $this->t->data('validation-errors', $validator->errors);
                $this->t->data('signup-name', $this->request->post('signup-name'));
                $this->t->data('signup-email', $this->request->post('signup-email'));
                $content = $this->render_view('login');
            }
        }
        elseif ( $this->request->page_is('logout') ) {
            $this->t->flash('You have been logged out.', 'success');
            User::logout_current();
            $content = $this->render_view('login');
        }
        else {
            $content = $this->render_view('login');
        }

        return $content;
    }

    #
    # Private
    #
    private function save_user_page($user) {
        $path = NeechyPath::join($this->html_path(), 'new-page.md.php');

        $page = Page::find_by_title($user->field('name'));
        $page->set('body', $this->t->render_partial_by_path($path));
        $page->set('editor', 'NeechySystem');
        $page->save();

        return $page;
    }
}
