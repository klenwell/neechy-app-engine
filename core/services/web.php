<?php
/**
 * core/services/web.php
 *
 * Neechy WebService class.
 *
 */
require_once('../core/services/base.php');
require_once('../core/neechy/config.php');
require_once('../core/neechy/errors.php');
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
    public function __construct($conf_path=NULL) {
        parent::__construct($conf_path);

        $this->request = NeechyRequest::load();
    }

    #
    # Public Methods
    #
    public function serve() {
        try {
            $this->start_session();
            $this->enforce_csrf_token();
            $response = $this->dispatch_to_handler();
        }
        catch (NeechyError $e) {
            $response = $this->dispatch_to_error($e);
        }

        $response->send_headers();
        $response->render();
    }

    #
    # Private Functions
    #
    private function start_session() {
        $session_name = md5(sprintf('%s.neechy', NeechyConfig::get('title', 'neechy')));
        session_name($session_name);
        session_start();
        return null;
    }

    private function enforce_csrf_token() {
        $this->set_csrf_token_if_not_set();
        $this->authenticate_csrf_token();
        return null;
    }

    private function set_csrf_token_if_not_set() {
        if ( ! isset($_SESSION['csrf_token']) ) {
            $_SESSION['csrf_token'] = sha1((string) microtime(true));
        }
        return $_SESSION['csrf_token'];
    }

    private function authenticate_csrf_token() {
        $posted_token = $this->request->post('csrf_token');
        $session_token = $_SESSION['csrf_token'];

        if ( $_POST ) {
            if ( ! $posted_token ) {
                throw new NeechyCsrfError('Authentication failed: No CSRF token');
            }
            elseif ( $posted_token != $session_token ) {
                throw new NeechyCsrfError('Authentication failed: CSRF token mismatch');
            }
        }

        return true;
    }

    private function dispatch_to_handler() {
        $handler = $this->load_handler($this->request);
        $content = $handler->handle();

        # Render web page
        $templater = NeechyTemplater::load();
        $templater->page = $handler->page;
        $templater->set('content', $content);
        $body = $templater->render();

        # Prepare response
        $response = new NeechyResponse($body, 200);
        return $response;
    }

    private function dispatch_to_error($e) {
        # Render web page
        $templater = NeechyTemplater::load();
        $templater->page = $handler->page;
        $templater->set('content', $e->getMessage());
        $body = $templater->render();

        # Prepare response
        $response = new NeechyResponse($body, 200);
        return $response;
    }

    private function load_handler($request) {
        $handler_app_path = NeechyPath::join(NEECHY_HANDLER_APP_PATH,
            $request->handler, 'handler.php');
        $handler_core_path = NeechyPath::join(NEECHY_HANDLER_CORE_PATH,
            $request->handler, 'handler.php');
        $HandlerClass = sprintf('%sHandler', ucwords($request->handler));

        if ( file_exists($handler_app_path) ) {
            require_once($handler_app_path);
        }
        elseif ( file_exists($handler_core_path) ) {
            require_once($handler_core_path);
        }
        else {
            throw new NeechyWebServiceError(sprintf('handler %s not found',
                $request->handler));
        }

        $handler = new $HandlerClass();
        return $handler;
    }
}
