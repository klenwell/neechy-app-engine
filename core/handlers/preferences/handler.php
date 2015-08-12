<?php
/**
 * core/handlers/preferences/handler.php
 *
 * PreferencesHandler class.
 *
 */
require_once('../core/handlers/base.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/templater.php');


class PreferencesHandler extends NeechyHandler {

    #
    # Public Methods
    #
    public function handle() {
        # Change password request
        if ( $this->request->action_is('change-password') ) {
            # TODO: process request
            var_dump($this->request);
            $content = $this->render_view('content');
        }

        # Default: display
        else {
            $content = $this->render_view('content');
        }

        return $content;
    }

    #
    # Private
    #
}
