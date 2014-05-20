<?php
/**
 * core/handlers/readme/handler.php
 *
 * ReadMe handler: a quick way to view (and edit) README content.
 *
 */
require_once('../core/handlers/page/handler.php');
require_once('../core/neechy/path.php');


class ReadMeHandler extends PageHandler {
    #
    # Public Methods
    #
    public function handle() {
        # Flash warning if user tries to save changes
        if ( $this->request->action_is('save') ) {
            $this->t->flash('Cannot update README.md file', 'warning');
        }

        # Set content
        $path = NeechyPath::root('README.md');
        $readme_body = file_get_contents($path);
        $this->t->data('editor', $this->t->render_editor($readme_body));

        # Partial variables
        $last_edited = '';
        $page_title = 'README.md';

        # Render partial
        $view_path = NeechyPath::root('core/handlers/page/html/content.html.php');
        $this->t->data('page-title', $page_title);
        $this->t->data('last-edited', $last_edited);
        $content = $this->render_view($view_path);
        return $content;
    }
}
