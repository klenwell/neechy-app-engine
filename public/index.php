<?php
/**
 * index.php
 *
 * This is the main Neechy script. This file is called each time a request is
 * made from the browser.
 *
 */
require_once('../core/libs/config.php');
require_once('../core/libs/request.php');
require_once('../core/libs/templater.php');
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

# This is the present
if ( $request->post('page-action') == 'save' ) {
    var_dump($_POST);
    $page->set('body', $request->post('page-body'));
    #$page->save();
    $content = $request->post('page-body');
}
elseif ( $page->is_new() ) {
    $content = $templater->render_editor();
}
else {
    $content = $page->field('body');
}

# Render web page
$templater->set('content', $content);
print $templater->render();
