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


class PageHandler extends NeechyHandler {
    #
    # Public Methods
    #
    public function handle() {
        # Partial variables
        $last_edited = sprintf('Last edited by %s on %s',
            $this->page->editor_link(),
            $this->page->field('created_at')
        );
        $page_title = NeechyTemplater::titleize_camel_case($this->page->get_title());

        # Render partial
        $this->t->data('page-title', $page_title);
        $this->t->data('panel-content', $this->page->body_to_html());
        $this->t->data('last-edited', $last_edited);

        # Return response
        if ( $this->request->format == 'ajax' ) {
            return new NeechyResponse($this->page->to_json(), 200);
        }
        else {
            $content = $this->render_view('content');
            return $this->respond($content);
        }
    }

    #
    # Private Methods
    #
    protected function respond($content) {
        # No AJAX response
        $templater = NeechyTemplater::load();
        $templater->page = $this->page;
        $templater->set('content', $content);
        $body = $templater->render();
        return new NeechyResponse($body, 200);
    }
}
