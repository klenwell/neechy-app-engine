<?php
require_once('../public/themes/bootstrap/php/helper.php');


class PageHelper extends BootstrapHelper {

    #
    # Properties
    #

    #
    # Constructor
    #
    #public function __construct($request=null) {
    #    $this->request = $request;
    #}

    #
    # Public Methods
    #
    public function build_page_tab_menu($page_title) {
        $page_tabs = array(
            # handler => array(label, href)
            'page' => array($page_title, '#read'),
            'edit' => array('Edit', '#edit'),
            'history' => array('History', '#history')
        );

        $tabs_by_user_status = array(
            'default' => array_keys($page_tabs),
            'logged-in' => array('page', 'history')
        );

        $user_status = ( User::is_logged_in() ) ? 'logged-in' : 'default';
        $user_tabs = $tabs_by_user_status[$user_status];

        $tab_links = array();

        foreach ( $user_tabs as $handler) {
            list($label, $href) = $page_tabs[$handler];
            $classes = array( $handler );

            if ( $handler == 'page' ) {
                $classes[] = 'title';
            }

            if ( $handler == $this->request->handler ) {
                $classes[] = 'active';
            }

            $tab_links[] = $this->build_page_tab_link($label, $href, $classes);
        }

        return implode("\n", $tab_links);
    }

    public function build_tab_panels($templater) {
        $page_tabs = array(
            # handler => content
            'page' => 'loading...',
            'edit' => $templater->data('editor'),
            'history' => $this->history_tab_content($templater)
        );

        $panel_divs = array();
        $panel_format = '<div class="%s" id="%s">%s</div>';

        foreach ( $page_tabs as $handler => $content ) {
            list($label, $href) = $page_tabs[$handler];
            $classes = array( 'tab-pane', $handler );

            if ( $handler == $this->request->handler ) {
                $classes[] = 'active';
            }

            $class_attr = implode(' ', $classes);
            $id = ( $handler == 'page' ) ? 'read' : $handler;

            $panel_divs[] = sprintf($panel_format, $class_attr, $id, $content);
        }

        return implode("\n", $panel_divs);
    }

    #
    # Private Methods
    #
    private function build_page_tab_link($label, $href, $classes=array()) {
        $tab_format = '<li class="%s"><a href="%s" data-toggle="tab">%s</a></li>';

        $class_attr = implode(' ', $classes);

        return sprintf($tab_format,
                       $class_attr,
                       $href,
                       $label);
    }

    private function history_tab_content($templater) {
        $html_f = <<<HTML5
    <div id="history-content">
        <h4>loading...</h4>
    </div>
HTML5;

        # Add script to dynamically load history on first click
        $script_f = <<<SCRIPT
        <script>
            (function() {
                var url = '/?page=%s&handler=history&format=ajax';

                // .one is .on that works just once.
                $('li.history').one('click', function() {
                    console.debug('load history tab', url);

                    $.get(url)
                        .success(function(html) {
                            console.debug('load history', html);
                            $('div#history-content').html(html);
                        });
                });
            })();
        </script>
SCRIPT;

        $templater->append_to_body(sprintf($script_f, $this->request->page));
        return $html_f;
    }
}
