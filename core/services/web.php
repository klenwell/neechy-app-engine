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
    public function __construct($conf_path=NULL) {
        parent::__construct($conf_path);

        $this->request = NeechyRequest::load();
        $this->page = Page::find_by_tag($this->request->page);
    }

    #
    # Public Methods
    #
    public function serve() {
        try {
            NeechySecurity::start_session();
            NeechySecurity::prevent_csrf();
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
    private function dispatch_to_handler() {
        $handler = $this->load_handler();
        $content = $handler->handle();

        # Render web page
        $templater = NeechyTemplater::load();
        $templater->page = $this->page;
        $templater->set('content', $content);
        $body = $templater->render();

        # Prepare response
        $response = new NeechyResponse($body, 200);
        return $response;
    }

    private function dispatch_to_error($e) {
        # Render web page
        $templater = NeechyTemplater::load();
        $templater->page = $this->page;
        $templater->set('content', $e->getMessage());
        $body = $templater->render();

        # Prepare response
        $response = new NeechyResponse($body, 200);
        return $response;
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
}
