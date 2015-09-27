<?php
/**
 * core/services/web.php
 *
 * Neechy WebService class.
 *
 */
require_once('../core/services/base.php');
require_once('../core/neechy/errors.php');
require_once('../core/neechy/security.php');
require_once('../core/neechy/request.php');
require_once('../core/neechy/templater.php');
require_once('../core/neechy/response.php');
require_once('../core/models/page.php');



class NeechyWebService extends NeechyService {
    #
    # Properties
    #
    private $request = NULL;

    #
    # Constructor
    #
    public function __construct($config) {
        parent::__construct($config);
    }

    #
    # Public Methods
    #
    public function serve() {
        try {
            NeechySecurity::start_session();
            NeechySecurity::prevent_csrf();
            $this->request = NeechyRequest::load();
            $this->validate_environment();
            $this->page = Page::find_by_title($this->request->page);
            $handler = $this->load_handler();
            $response = $handler->handle();
        }
        catch (NeechyError $e) {
            $handler = $this->load_error_handler();
            $response = $handler->handler($e);
            #$content = $e->getMessage();
        }

        $response->send_headers();
        $response->render();
    }

    #
    # Private Functions
    #
    private function validate_environment() {
        if ( NeechyConfig::environment() == 'app' ) {
            return true;
        }
        elseif ( NeechyConfig::environment() == 'test' ) {
            $this->setup_dev_environment();
            return true;
        }
        else {
            $format = 'Config file missing. Please see %s for install help.';
            $link = '<a href="https://github.com/klenwell/neechy">Neechy README file</a>';
            throw new NeechyConfigError(sprintf($format, $link));
        }
    }

    private function setup_dev_environment() {
        $handler_path = NeechyPath::join(NEECHY_HANDLER_CORE_PATH,
            'install', 'handler.php');
        require_once($handler_path);

        if ( NeechyDatabase::database_exists(NeechyConfig::get('mysql_database')) ) {
            error_log('Test database found.');
        }
        else {
            error_log('Setting up dev environment using test configuration file.');

            # Buffer console output.
            ob_start();
            $handler = new InstallHandler($this->request);
            $handler->setup_dev();
            ob_end_clean();
        }
    }

    private function load_handler() {
        $handler_app_path = NeechyPath::join(NEECHY_HANDLER_APP_PATH,
            $this->request->handler, 'handler.php');
        $handler_core_path = NeechyPath::join(NEECHY_HANDLER_CORE_PATH,
            $this->request->handler, 'handler.php');
        $HandlerClass = sprintf('%sHandler', ucwords($this->request->handler));

        if ( file_exists($handler_app_path) ) {
            require_once($handler_app_path);
        }
        elseif ( file_exists($handler_core_path) ) {
            require_once($handler_core_path);
        }
        else {
            throw new NeechyWebServiceError(sprintf('handler %s not found',
                $this->request->handler));
        }

        $handler = new $HandlerClass($this->request, $this->page);
        return $handler;
    }

    private function load_error_handler() {
        $handler_path = NeechyPath::join(NEECHY_HANDLER_CORE_PATH, 'error', 'handler.php');
        require_once($handler_path);
        $handler = new ErrorHandler($this->request, $this->page);
        return $handler;
    }
}
