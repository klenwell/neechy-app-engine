<?php
/**
 * app/handlers/admin/handler.php
 *
 * AuthHandler class.
 *
 */
require_once('../core/handlers/base.php');
require_once('../app/services/auth.php');

require_once('../core/neechy/path.php');
require_once('../core/neechy/response.php');
require_once('../core/models/user.php');
require_once('../core/models/page.php');
require_once('../core/handlers/auth/php/validator.php');





class AdminHandler extends NeechyHandler {

    #
    # Public Methods
    #
    public function handle() {
        $auth = AppAuthService::redirect_user_if_not_admin();
        $content = $this->route();
        return $this->respond($content);
    }

    #
    # Private
    #
    protected function route() {
        if ( $this->request->action_is('test') ) {
            return 'Admin test successful.';
        }
        else {
            return $this->view_dashboard();
        }
    }

    protected function view_dashboard() {
        $this->t->data('db', NeechyDatabase::connect_to_db());
        return $this->render_view('dashboard');
    }

    protected function html_path() {
        return NeechyPath::join(NEECHY_HANDLER_APP_PATH, $this->folder_name(), 'html');
    }
}
