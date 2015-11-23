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
        $wml = $this->wikka_links_to_markdown_links($wml);
        $html = $this->markdown->text($wml);
        return $html;
    }

    #
    # Protected Methods
    #
    protected function wikka_links_to_markdown_links($wml) {
        $wml = $this->replace_double_bracket_links($wml);
        $wml = $this->replace_title_case_links($wml);
        return $wml;
    }

    protected function replace_title_case_links($wml) {
        # Source: http://stackoverflow.com/a/815849/1093087
        $regex_parts = array(
            '(?<=\s|^)',    # either a word boundary or position 0
                            # avoid cases like [Already in markup link](TitleCase)
                            # http://stackoverflow.com/a/14074352/1093087
            '[A-Z]',        # capital letter
            '[a-zA-Z]*',    # all alphabetic chars
            '(?:',          # look-behind?
                '[a-z][a-zA-Z]*[A-Z]',
                '|',
                '[A-Z][a-zA-Z]*[a-z]',
            ')',
            '[a-zA-Z]*'     # all alphabetic chars
        );
        $regex = sprintf('/%s/msu', join('', $regex_parts));
        $wml = preg_replace_callback($regex,
                                     array($this, 'title_case_to_markdown_link'),
                                     $wml);
        return $wml;
    }

    protected function replace_double_bracket_links($wml) {
        $regex_parts = array("\[\[\s*(.*?)\s*\]\]");
        $regex = sprintf('/%s/msu', join('', $regex_parts));
        $wml = preg_replace_callback($regex,
                                     array($this, 'double_brackets_to_markdown_link'),
                                     $wml);
        return $wml;
    }

    protected function title_case_to_markdown_link($matches) {
        $markdown_format = '[%s](/page/%s)';
        $camel_case = $matches[0];
        return sprintf($markdown_format, $camel_case, $camel_case);
    }

    protected function double_brackets_to_markdown_link($matches) {
        $markdown_format = '[%s](%s)';
        $meat = $matches[1];

        if ( strpos($meat, '|') !== false ) {
            list($href, $label) = explode('|', $meat, 2);
        }
        else {
            list($href, $label) = explode(' ', $meat, 2);
        }

        $protocol_regex = sprintf('~(%s)://~su', 'http|https|ftp|news|irc|gopher');
        $with_protocol = preg_match($protocol_regex, $href);
        if ( $with_protocol ) {
            $href = trim($href);
        }
        else {
            $href = sprintf('/page/%s', trim($href));
        }

        return sprintf($markdown_format, trim($label), $href);
    }
}
