<?php
/**
 * core/handlers/editor/handler.php
 *
 * EditorHandler class
 *
 */
require_once('../core/handlers/base.php');
require_once('../core/neechy/templater.php');
require_once('../core/neechy/response.php');


class EditorHandler extends NeechyHandler {
    #
    # Public Methods
    #
    public function handle() {
        # Action tree
        if ( $this->request->action_is('save') ) {
            $this->page->set('body', $this->request->post('wmd-input'));
            $this->page->save();
            NeechyResponse::redirect($this->page->url());
        }
        elseif ( $this->request->action_is('preview') ) {
            $markdown = new Parsedown();
            $preview_html = $markdown->text($this->request->post('wmd-input'));
            $this->t->data('preview', $preview_html);
            $this->t->data('page-body', $this->request->post('wmd-input'));
        }
        elseif ( $this->request->action_is('edit') ) {
            $this->t->data('page-body', $this->request->post('wmd-input'));
        }
        else {
            $this->t->data('page-body', $this->page->field('body'));
        }

        # Partial variables
        $last_edited = sprintf('Last edited by %s on %s',
            $this->page->editor_link(),
            $this->page->field('created_at')
        );
        $page_title = NeechyTemplater::titleize_camel_case($this->page->get_title());

        # Render partial
        $this->t->data('action', $this->request->action);
        $this->t->data('page-title', $page_title);
        $this->t->data('last-edited', $last_edited);
        $content = $this->render_view('editor');

        # Return response
        return $this->respond($content);
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
