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


class ErrorHandler extends NeechyHandler {
    #
    # Public Methods
    #
    public function handle($e) {
        $content = $this->render_error($e);
        return $this->respond($content, $e->status_code);
    }

    #
    # Private Methods
    #
    protected function render_error($e) {
        $format = <<<HTML5
    <div class="error">
        <h2>%s</h2>
        <p>%s</p>
    </div>
HTML5;

        return sprintf($format, $e->status_code, $e->getMessage());
    }
}
