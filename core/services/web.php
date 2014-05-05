<?php
/**
 * core/services/web.php
 *
 * Neechy WebService class.
 *
 */
require_once('../core/services/base.php');
require_once('../core/neechy/request.php');
require_once('../core/neechy/templater.php');
require_once('../core/neechy/response.php');
require_once('../core/models/page.php');



class NeechyWebService extends NeechyService {
    #
    # Properties
    #
    private $request = NULL;

    #
    # Constructor
    #
    public function __construct($conf_path=NULL) {
        parent::__construct($conf_path);

        $this->request = new NeechyRequest();
        $this->templater = new NeechyTemplater();
        $this->page = Page::find_by_tag('HomePage');
    }

    #
    # Public Methods
    #
    public function serve() {
        # TODO: This is the future
        #require_once('../core/handlers/edit/handler.php');
        #$handler = new EditHandler($request);
        #$content = $handler->handle();

        return $this->interim_serve();
    }

    public function serve_error() {
    }

    #
    # Private Functions
    #
    private function interim_serve() {
        #
        # TODO: replace this.
        #
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

        if ( $this->request->post('page-action') == 'save' ) {
            $this->page->set('body', $this->request->post('page-body'));
            $this->page->save();
            $content = sprintf($page_tabs_f, $this->templater->render_editor(
                $this->page->field('body')));
        }
        elseif ( $this->page->is_new() ) {
            $content = $this->templater->render_editor();
        }
        else {
            $content = sprintf($page_tabs_f, $this->templater->render_editor(
                $this->page->field('body')));
        }

        # Render web page
        $this->templater->page = $this->page;
        $this->templater->set('content', $content);

        # Prepare response
        $body = $this->templater->render();
        $response = new NeechyResponse($body, 200);
        return $response;
    }
}
