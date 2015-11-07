<?php
require_once('../core/neechy/path.php');

class PageTabMenu {
    public $ul_id = '';
    public $request = null;
    public $tabs = array();

    public function __construct($request, $ul_id='') {
        $this->request = $request;
        $this->ul_id = $ul_id;
    }

    public function add_tabs($tabs) {
        foreach ( $tabs as $params ) {
            $tab = new PageTab($params[0], $params[1], $params[2]);
            $this->tabs[] = $tab;
        }
    }

    public function render() {
        $format = '<ul%s class="nav nav-tabs">%s</ul>';

        $ul_id = ( $this->ul_id ) ? sprintf(' id="%s"', $this->ul_id) : '';

        $tabs = array();

        for ( $n=0; $n < count($this->tabs); $n++ ) {
            $tab = $this->tabs[$n];
            $tabs[] = $tab->render($this->request);
        }

        return sprintf($format, $ul_id, join('', $tabs));
    }
}

class PageTab {
    public $label = '';
    public $handler = '';
    public $page_slug = '';
    public $li_class = 'page-tab';

    public function __construct($label, $handler, $page_slug) {
        $this->label = $label;
        $this->handler = $handler;
        $this->page_slug = $page_slug;
    }

    public function is_active($request) {
        return $request->handler == $this->handler;
    }

    public function render($request) {
        $tab_format = '<li class="%s"><a href="%s" data-toggle-off="tab">%s</a></li>';

        if ( $this->is_active($request) ) {
            $this->li_class = sprintf('%s active', $this->li_class);
        }

        $link = NeechyPath::url($this->handler, $this->page_slug);

        return sprintf($tab_format, $this->li_class, $link, $this->label);
    }
}
