<?php
/**
 * index.php
 *
 * This is the main Neechy script. This file is called each time a request is
 * made from the browser.
 *
 */
require_once('../core/neechy/config.php');
require_once('../core/neechy/request.php');
require_once('../core/neechy/templater.php');
require_once('../core/models/page.php');


# Init config here for usage elsewhere
NeechyConfig::init();

$request = new NeechyRequest();
$templater = new NeechyTemplater();
$page = Page::find_by_tag('HomePage');

# TODO: This is the future
#require_once('../core/handlers/edit/handler.php');
#$handler = new EditHandler($request);
#$content = $handler->handle();

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

# This is the present
if ( $request->post('page-action') == 'save' ) {
    $page->set('body', $request->post('page-body'));
    $page->save();
    $content = sprintf($page_tabs_f, $templater->render_editor($page->field('body')));
}
elseif ( $page->is_new() ) {
    $content = $templater->render_editor();
}
else {
    $content = sprintf($page_tabs_f, $templater->render_editor($page->field('body')));
}

# Render web page
$templater->page = $page;
$templater->set('content', $content);
print $templater->render();
