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
require_once('../app/models/user.php');
require_once('../app/models/page.php');
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
            return '<h4>Admin access test is successful.</h4>';
        }
        elseif ( $this->request->action_is('reset-db') ) {
            $this->reset_database();
        }
        elseif ( $this->request->action_is('install-db') ) {
            $this->install_database();
        }
        else {
            # Default action: show dashboard
        }

        return $this->view_dashboard();
    }

    protected function reset_database() {
        $confirm_text = 'RESET';

        if ( $this->request->post('confirmed-reset-db') ) {
            if ( $this->request->post('confirmed-reset-db') == $confirm_text ) {
                $this->drop_tables();
                NeechyDatabase::create_model_tables();
                AppUser::create_on_install();
                AppPage::create_on_install();
                $this->t->flash('Dropped and recreated database tables.', 'success');
            }
            else {
                $this->t->flash('Failed to confirm reset.', 'warning');
            }
        }
        else {
            $this->t->data('confirm-reset-db', true);
        }

        return null;
    }

    protected function install_database() {
        $tables = NeechyDatabase::create_model_tables();
        AppUser::create_on_install();
        AppPage::create_on_install();
        $this->t->flash('Created database tables.', 'success');
        return $tables;
    }

    protected function view_dashboard() {
        $tables = $this->load_tables();

        $database_installed = false;
        foreach ( $tables as $table_name => $table ) {
            if ( $table['exists'] ) {
                $database_installed = true;
            }
        }

        $this->t->data('tables', $tables);
        $this->t->data('database_installed', $database_installed);
        return $this->render_view('dashboard');
    }

    protected function html_path() {
        return NeechyPath::join(NEECHY_HANDLER_APP_PATH, $this->folder_name(), 'html');
    }

    protected function load_tables() {
        $tables = array();

        foreach ( NeechyDatabase::core_model_classes() as $model_class ) {
            $table_name = $model_class::table_name();
            $table_exists = $model_class::table_exists();
            $count = ( ! $table_exists ) ? 'N/A' : $model_class::count();

            $tables[$table_name] = array(
                'exists' => $table_exists,
                'count' => $count
            );
        }

        return $tables;
    }

    protected function drop_tables() {
        $dropped_tables = array();

        foreach ( NeechyDatabase::core_model_classes() as $model_class ) {
            $model = $model_class::drop_table_if_exists();
            $dropped_tables[] = $model->table;
        }

        return $dropped_tables;
    }
}
