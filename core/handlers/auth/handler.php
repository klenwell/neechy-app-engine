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


class AuthHandler extends NeechyHandler {
    #
    # Properties
    #
    public $page = null;

    #
    # Public Methods
    #
    public function handle() {
        $templater = NeechyTemplater::load();

        if ( $this->request->action_is('login') ) {
            $this->t->data('alert', 'logging in');
            $content = $this->render_view('login');
        }
        elseif ( $this->request->action_is('register') ) {
            $this->t->data('alert', 'signing up');
            $content = $this->render_view('login');
        }
        else {
            $content = $this->render_view('login');
        }

        return $content;
    }
}
