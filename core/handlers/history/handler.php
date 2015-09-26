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
        $this->t->data('edits', $edits);
        return $this->render_view('table');
    }

    #
    # Private Methods
    #
}
