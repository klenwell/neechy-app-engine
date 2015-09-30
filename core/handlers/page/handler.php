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
        # Action tree
        if ( $this->request->action_is('save') ) {
            $this->page->set('body', $this->request->post('page-body'));
            $this->page->save();
            NeechyResponse::redirect($this->page->url());
        }
        elseif ( $this->page->is_new() ) {
            #$this->t->data('editor', $this->t->render_editor());
        }
        else {
            #$this->t->data('editor', $this->t->render_editor($this->page->field('body')));
        }

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
        $content = $this->render_view('content');

        # Return response
        if ( $this->request->format == 'json' ) {
            return new NeechyResponse($this->page->to_json(), 200);
        }
        else {
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

    private function render_page_controls() {
        $format = <<<HTML5
      <div id="page-controls" class="navbar">
        <div class="container">
          <ul class="nav navbar-nav">
            <li><p class="navbar-text">%s</p></li>
          </ul>
        </div>
      </div>
HTML5;

        $last_edited = sprintf('Last edited by %s on %s',
            $this->page->editor_link(),
            $this->page->field('saved_at')
        );

        return sprintf($format, $last_edited);
    }
}
