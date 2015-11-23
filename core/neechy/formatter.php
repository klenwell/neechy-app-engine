<?php
/**
 * core/neechy/formatter.php
 *
 * Injects Parsedown class to translate custom wiki syntax into HTML.
 *
 */
require_once('../lib/parsedown/Parsedown.php');


class NeechyDown extends Parsedown {
    protected function inlineLink($Excerpt)
    {
        $Element = array(
            'name' => 'a',
            'handler' => 'line',
            'text' => null,
            'attributes' => array(
                'href' => null,
                'title' => null,
            ),
        );

        $extent = 0;
        $remainder = $Excerpt['text'];

        $extract = $this->extractWikiStyleLink($remainder);

        // Wiki style: [[href label here]]
        if ( $extract ) {
            list($label, $href) = $this->parseWikiLink($extract[1]);
            $extent = strlen($extract[0]);
            $Element['text'] = $label;
            $Element['attributes']['href'] = $href;
        }
        // Markdown style: [label](href)
        else {
            // Match label (bracketed element)
            if (preg_match('/\[((?:[^][]|(?R))*)\]/', $remainder, $matches))
            {
                $Element['text'] = $matches[1];

                $extent += strlen($matches[0]);

                $remainder = substr($remainder, $extent);
            }
            else
            {
                return;
            }

            // Match href
            if (preg_match('/^[(]((?:[^ ()]|[(][^ )]+[)])+)(?:[ ]+("[^"]*"|\'[^\']*\'))?[)]/', $remainder, $matches))
            {
                $Element['attributes']['href'] = $matches[1];

                if (isset($matches[2]))
                {
                    $Element['attributes']['title'] = substr($matches[2], 1, - 1);
                }

                $extent += strlen($matches[0]);
            }
            else
            {
                if (preg_match('/^\s*\[(.*?)\]/', $remainder, $matches))
                {
                    $definition = strlen($matches[1]) ? $matches[1] : $Element['text'];
                    $definition = strtolower($definition);

                    $extent += strlen($matches[0]);
                }
                else
                {
                    $definition = strtolower($Element['text']);
                }

                if ( ! isset($this->DefinitionData['Reference'][$definition]))
                {
                    return;
                }

                $Definition = $this->DefinitionData['Reference'][$definition];

                $Element['attributes']['href'] = $Definition['url'];
                $Element['attributes']['title'] = $Definition['title'];
            }
        }

        $Element['attributes']['href'] = str_replace(array('&', '<'), array('&amp;', '&lt;'), $Element['attributes']['href']);

        return array(
            'extent' => $extent,
            'element' => $Element,
        );
    }

    protected function extractWikiStyleLink($text) {
        $matches = array();
        $regex_parts = array(
            '\[\[',             # start double brackets
            '\s*(.*?)\s*',      # inner content
            '\]\]'              # end double brackets
        );
        $regex = sprintf('/%s/msu', join('', $regex_parts));
        $matched = preg_match($regex, $text, $matches);

        if ( $matched ) {
            return $matches;
        }
        else {
            return false;
        }
    }

    protected function parseWikiLink($link) {
        if ( strpos($link, '|') !== false ) {
            list($href, $label) = explode('|', $link, 2);
        }
        else {
            list($href, $label) = explode(' ', $link, 2);
        }

        $protocol_regex = sprintf('~(%s)://~su', 'http|https|ftp|news|irc|gopher');
        $with_protocol = preg_match($protocol_regex, $href);
        if ( $with_protocol ) {
            $href = trim($href);
        }
        else {
            $href = sprintf('/page/%s', trim($href));
        }

        return array(trim($label), $href);
    }
}


class NeechyFormatter  {

    #
    # Properties
    #
    public $markdown = null;

    #
    # Constructor
    #
    public function __construct() {
        $this->markdown = new NeechyDown();
    }

    #
    # Public Static Methods
    #

    #
    # Public Methods
    #
    public function wml_to_html($wml) {
        #$wml = $this->wikka_links_to_markdown_links($wml);
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
