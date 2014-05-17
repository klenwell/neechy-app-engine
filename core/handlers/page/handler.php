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
            $this->t->data('editor', $this->t->render_editor());
        }
        else {
            $this->t->data('editor', $this->t->render_editor($this->page->field('body')));
        }

        # Partial variables
        $last_edited = sprintf('Last edited by %s on %s',
            $this->page->editor_link(),
            $this->page->field('saved_at')
        );
        $page_title = NeechyTemplater::titleize_camel_case(
            $this->page->field('tag', 'Page')
        );

        # Render partial
        $this->t->data('page-title', $page_title);
        $this->t->data('last-edited', $last_edited);
        $content = $this->t->render_partial_by_id('content');
        return $content;
    }

    #
    # Private Methods
    #
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
