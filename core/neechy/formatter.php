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
            $parts = explode(' ', $link, 2);
            if ( count($parts) > 1 ) {
                list($href, $label) = $parts;
            }
            else {
                list($href, $label) = array($parts[0], $parts[0]);
            }
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
        $html = $this->markdown->text($wml);
        return $html;
    }

    #
    # Protected Methods
    #
}
