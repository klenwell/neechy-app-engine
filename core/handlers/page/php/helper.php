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
            # handler => label
            'page' => $page_title,
            'editor' => 'Edit',
            'history' => 'History'
        );

        $tabs_by_user_status = array(
            'default' => array('page', 'history'),
            'logged-in' => array_keys($page_tabs)
        );

        $user_status = ( User::is_logged_in() ) ? 'logged-in' : 'default';
        $user_tabs = $tabs_by_user_status[$user_status];

        $tab_links = array();

        foreach ( $user_tabs as $handler) {
            $label = $page_tabs[$handler];
            $href = NeechyPath::url($handler, $page_title);
            $classes = array( $handler );

            if ( $handler == 'page' ) {
                $classes[] = 'title';
            }

            if ( $handler == $this->request->handler ) {
                $classes[] = 'active';
            }

            if ( $this->request->handler == 'editor' &&
                 $handler == $this->request->handler &&
                 $this->request->action == 'preview' ) {
                $label = 'Preview';
            }

            $tab_links[] = $this->build_page_tab_link($label, $href, $classes);
        }

        return implode("\n", $tab_links);
    }

    public function build_tab_panels($panel_content) {
        $page_tabs = array(
            # handler => content
            'page' => 'loading...',
            'editor' => 'loading...',
            'history' => 'loading...'
        );
        $page_tabs[$this->request->handler] = $panel_content;

        $panel_divs = array();
        $panel_format = '<div class="%s" id="%s">%s</div>';

        foreach ( $page_tabs as $handler => $content ) {
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
        # Change data-toggle-off to data-toggle below to reenable Bootstrap tab panels.
        $tab_format = '<li class="%s"><a href="%s" data-toggle-off="tab">%s</a></li>';

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
