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
        $page_tabs_f = <<<HTML5
<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="read"></div>
  <div class="tab-pane" id="edit">%s</div>
  <div class="tab-pane" id="discuss">Under development</div>
  <div class="tab-pane" id="history">Under development</div>
  <div class="tab-pane" id="access">Under development</div>
</div>
HTML5;

        if ( $this->request->action_is('save') ) {
            $this->page->set('body', $this->request->post('page-body'));
            $this->page->save();
            NeechyResponse::redirect($this->page->url());
        }
        elseif ( $this->page->is_new() ) {
            $content = $this->t->render_editor();
        }
        else {
            $content = sprintf($page_tabs_f, $this->t->render_editor(
                $this->page->field('body')));
        }

        $this->t->set('page-controls', $this->render_page_controls());
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
