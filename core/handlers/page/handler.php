<?php
/**
 * core/handlers/page/handler.php
 *
 * PageHandler class.
 *
 */
require_once('../core/handlers/base.php');


class PageHandler extends NeechyHandler {
    #
    # Properties
    #
    public $page = null;
    public $request = null;

    #
    # Constructor
    #
    public function __construct($request) {
        parent::__construct();

        $this->request = $request;

        # TODO: get page tag from request
        $tag = 'HomePage';
        $this->page = Page::find_by_tag($tag);
    }

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

        $templater = NeechyTemplater::load();

        if ( $this->request->post('page-action') == 'save' ) {
            $this->page->set('body', $this->request->post('page-body'));
            $this->page->save();
            $content = sprintf($page_tabs_f, $templater->render_editor(
                $this->page->field('body')));
        }
        elseif ( $this->page->is_new() ) {
            $content = $templater->render_editor();
        }
        else {
            $content = sprintf($page_tabs_f, $templater->render_editor(
                $this->page->field('body')));
        }

        return $content;
    }
}
