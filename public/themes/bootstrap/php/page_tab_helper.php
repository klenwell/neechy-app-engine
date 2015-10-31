<?php
require_once('../public/themes/bootstrap/php/helper.php');


class PageTabHelper extends BootstrapHelper {

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
    public function new_build_page_tab_menu($page) {
        $handler = $this->request->handler;
        $page_title = $this->request->action;
        $user_status = ( User::is_logged_in() ) ? 'editor' : 'reader';

        $page_tab_item = array($page->get_title(), $page->url());
        $history_tab_item = array('History', $page->url('history'));

        if ( $user_status == 'editor' ) {
            $editor_tab_item = array('Edit', $page->url('editor'));
            $tab_items = array($page_tab_item, $editor_tab_item, $history_tab_item);
        }
        else {
            $tab_items = array($page_tab_item, $history_tab_item);
        }

        return $helper->build_tab_menu($tab_items, array('id' => 'page-tabs'));

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

    public function build_tab_menu($tab_items, $ul_attrs=array()) {
        # $tab_items will be passed to build_tab_list_items below. $ul_attrs will be
        # an array of attrs for the wrapping. See build_tab_list_items for items on
        # $tab_items.
        #
        # Usage:
        # $ul_attrs = array('id' => 'page-tabs', 'class' => 'nav nav-tabs');
        # $helper->build_tab_menu($tab_items, $ul_attrs);
        $ul_format = '<ul%s>%s</ul>';
        $bootstrap_ul_class = 'nav nav-tabs';

        if ( isset($ul_attrs['class']) &&
            strpos($ul_attrs['class'], $bootstrap_ul_class) === false ) {
            $ul_attrs['class'] = sprintf('%s %s', $bootstrap_ul_class, $ul_attrs['class']);
        }
        else {
            $ul_attrs['class'] = $bootstrap_ul_class;
        }

        $ul_attr_str = $this->array_to_attr_string($a_attrs);
        $tab_lis = $this->build_tab_list_items($tab_items);

        return sprintf($ul_format, $ul_attr_str, join('', $tab_lis));
    }

    public function build_tab_list_items($tab_items) {
        # $tab_items array should be an array of arrays with following items:
        # array(label, href, li_attrs, a_attrs)
        #
        # Usage:
        # $tab_items = array(
        #   array('home', '/', array('id' => 'home-tab'), array('data-toggle-off' => 'tab'))
        # );
        # $lis = $helper->build_tab_list_items($tab_items);
        $tab_list_items = array();

        foreach ( $tab_items as $tab_item ) {
            $label = $tab_item[0];
            $href = $tab_item[1];
            $li_attrs = (count($tab_item) > 1) ? $tab_item[2] : array();
            $a_attrs = (count($tab_item) > 2) ? $tab_item[3] : array();
            $li = $this->build_tab_list_item($label, $href, $li_attrs, $a_attrs);
            $tab_list_items[] = $li;
        }

        return $tab_list_items;
    }

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
    # Protected Methods
    #
    protected function build_tab_list_item($label, $href, $li_attrs=array(), $a_attrs=array()) {
        $li_format = '<li%s><a href="%s"%s>%s</a></li>';
        $li_attr_str = $this->array_to_attr_string($li_attrs);
        $a_attr_str = $this->array_to_attr_string($a_attrs);
        return sprintf($li_format, $li_attr_str, $href, $a_attr_str, $label);
    }

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
