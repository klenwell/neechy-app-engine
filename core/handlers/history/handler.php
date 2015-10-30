<?php
/**
 * core/handlers/page/handler.php
 *
 * PageHandler class.
 *
 */
require_once('../core/handlers/base.php');
require_once('../core/neechy/templater.php');
require_once('../core/neechy/response.php');


class HistoryHandler extends NeechyHandler {
    public $page = null;

    #
    # Public Methods
    #
    public function handle() {
        $page_id = $this->request->param(0);


        if ( $page_id ) {
            return $this->show_page_version($page_id);
        }
        else {
            return $this->show_index();
        }
    }

    #
    # Private Methods
    #
    private function show_index() {
        $page_title = $this->request->action;
        $page = Page::find_by_title($page_title);
        $edits = $page->load_history();

        if ( $this->request->format == 'ajax' ) {
            return new NeechyResponse(json_encode($edits), 200);
        }
        else {
            $this->t->data('edits', $edits);
            $content = $this->render_view('table');
            return $this->respond($content);
        }
    }

    private function show_page_version($page_id) {
        $page = Page::init();
        $page = $page->find_by_id($page_id);
        $this->t->data('page', $page);
        $content = $this->render_view('show');
        return $this->respond($content);
    }
}
