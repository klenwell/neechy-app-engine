<?php
/**
 * core/neechy/formatter.php
 *
 * Injects Parsedown class to translate custom wiki syntax into HTML.
 *
 */
require_once('../lib/parsedown/Parsedown.php');


class NeechyFormatter  {

    #
    # Properties
    #
    public $markdown = null;

    #
    # Constructor
    #
    public function __construct() {
        $this->markdown = new Parsedown();
    }

    #
    # Public Static Methods
    #

    #
    # Public Methods
    #
    public function wml_to_html($wml) {
        $wml = $this->wikka_links_to_markup_links($wml);
        $html = $this->markdown->text($wml);
        return $html;
    }

    #
    # Protected Methods
    #
    protected function wikka_links_to_markup_links($wml) {
        # TODO
        return $wml;
    }
}
