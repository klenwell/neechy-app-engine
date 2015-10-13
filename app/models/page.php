<?php
/**
 * app/models/page.php
 *
 * App Page model class.
 *
 * Slug is a normalized version of the title. It is used to minimize minor variations
 * of page titles.
 *
 */
require_once('../core/models/page.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/templater.php');


class AppPage extends Page {

    protected static $core_pages = array(
        'home',
        'NeechyFormatting',
        NEECHY_USER
    );

    #
    # Static Methods
    #
    public static function create_on_install() {
        $pages_created = array();
        $pages_dir = NeechyPath::join(NEECHY_APP_PATH, 'templates/core_pages');
        $templater = NeechyTemplater::load();

        foreach(self::$core_pages as $name) {
            $basename = sprintf('%s.md.php', $name);
            $path = NeechyPath::join($pages_dir, $basename);
            $page_body = $templater->render_partial_by_path($path);

            $page = Page::find_by_title($name);
            $page->set('body', $page_body);
            $page->set('editor', NEECHY_USER);
            $page->save();
            $pages_created[] = $page;
        }

        return $pages_created;
    }
}
