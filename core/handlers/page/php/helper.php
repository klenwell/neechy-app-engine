<?php
require_once('../public/themes/bootstrap/php/helper.php');
require_once('../app/models/user.php');
require_once('../public/themes/bootstrap/php/page_tab_menu.php');


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
    public function build_page_tab_menu($page) {
        $user_logged_in = AppUser::is_logged_in();
        $page_slug = $page->field('slug');

        $read_tab = array($page->title(), 'page', $page_slug);
        $edit_tab = array('Edit', 'editor', $page_slug);
        $history_tab = array('History', 'history', $page_slug);

        $tab_menu = new PageTabMenu($this->request, 'page-tabs');

        if ( $user_logged_in ) {
            $tab_menu->add_tabs(array($read_tab,
                                      $edit_tab,
                                      $history_tab));
        }
        else {
            $tab_menu->add_tabs(array($read_tab,
                                      $history_tab));
        }

        return $tab_menu->render();
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
