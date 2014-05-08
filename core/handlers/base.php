<?php
/**
 * core/handlers/base.php
 *
 * NeechyHandler base class.
 *
 */
require_once('../core/neechy/request.php');



class NeechyHandler {
    #
    # Properties
    #
    public $request = NULL;
    public $page = NULL;

    #
    # Constructor
    #
    public function __construct() {
        $this->request = NeechyRequest::load();
        $this->page = Page::find_by_tag($this->request->page);
    }

    #
    # Public Methods
    #
    public function handle() {
    }
}
