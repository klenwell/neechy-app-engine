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
    # Constants
    #
    const DEFAULT_PAGE = 'home';

    #
    # Public Methods
    #
    public function handle() {
        $page_slug = ( $this->request->action ) ? $this->request->action : self::DEFAULT_PAGE;
        $this->page = Page::find_by_slug($page_slug);

        # Partial variables
        $last_edited = sprintf('Last edited by %s on %s',
            $this->page->editor_link(),
            $this->page->field('created_at')
        );

        # Render partial
        $this->t->data('page', $this->page);
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
    protected function respond($content, $status=200) {
        # No AJAX response
        $templater = NeechyTemplater::load();
        $templater->page = $this->page;
        $templater->set('content', $content);
        $body = $templater->render();
        return new NeechyResponse($body, $status);
    }
}
