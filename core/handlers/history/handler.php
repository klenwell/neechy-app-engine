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
    #
    # Public Methods
    #
    public function handle() {
        $edits = $this->page->load_history();

        if ( $this->request->format == 'ajax' ) {
            return new NeechyResponse(json_encode($edits), 200);
        }
        else {
            $this->t->data('edits', $edits);
            $content = $this->render_view('table');
            return $this->respond($content);
        }
    }

    #
    # Private Methods
    #
}
