<?php
/**
 * core/handlers/base.php
 *
 * NeechyHandler base class.
 *
 */
require_once('../core/neechy/constants.php');
require_once('../core/neechy/templater.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/errors.php');
require_once('../core/models/page.php');



class NeechyHandler {
    #
    # Properties
    #
    public $request = NULL;
    public $page = NULL;
    public $t = NULL;

    #
    # Constructor
    #
    public function __construct($request=null, $page=null) {
        $this->request = $request;
        $this->page = $page;
        $this->t = NeechyTemplater::load();
        $this->t->data('handler', get_class($this));
    }

    #
    # Public Methods
    #
    public function handle() {
        throw new NeechyError('NeechyHandler::handler should be overridden');
    }

    public function render_view($id_or_path) {
        # First look for partial file in handler's html folder. If not there,
        # user theme's html folder.
        $fname = sprintf('%s.html.php', $id_or_path);
        $handler_view = NeechyPath::join($this->html_path(), $fname);

        if ( file_exists($handler_view) ) {
            return $this->t->render_partial_by_path($handler_view);
        }
        elseif ( file_exists($id_or_path) ) {
            return $this->t->render_partial_by_path($id_or_path);
        }
        else {
            return $this->t->render_partial_by_id($id_or_path);
        }
    }

    #
    # Protected Methods
    #
    protected function html_path() {
        return NeechyPath::join(NEECHY_HANDLER_CORE_PATH, $this->folder_name(), 'html');
    }

    protected function folder_name() {
        $class_name = get_class($this);
        $folder_name = str_replace('Handler', '', $class_name);
        return strtolower($folder_name);
    }
}
