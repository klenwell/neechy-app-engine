<?php
/**
 * core/neechy/path.php
 *
 * Various utility classes.
 *
 */


class NeechyPath {
    #
    # Static Methods
    #
    static public function join() {
        #
        # Based on http://stackoverflow.com/a/15575293/1093087
        #
        $args = func_get_args();
        $subpaths = array();

        foreach ( $args as $subpath ) {
            $subpath = trim($subpath);
            if ( $subpath ) {
                $subpaths[] = $subpath;
            }
        }

        return preg_replace('#/+#', '/', implode('/', $subpaths));
    }

    static public function abspath($path) {
        return realpath($path);
    }

    static public function root($sub_path='') {
        $root = NeechyPath::abspath(NeechyPath::join(__DIR__, '../..'));
        return NeechyPath::join($root, $sub_path);
    }

    static public function old_url($page, $handler=NULL, $action=NULL, $params=array()) {
        # TODO: Detect rewrite mode; Add query param support
        $parts = array(
            'page' => $page,
            'handler' => $handler,
            'action' => $action
        );
        $keys = array_keys($parts);

        if ( $rewrite_mode = FALSE ) {
            $url_parts = array();

            foreach ($keys as $key) {
                if ( ! is_null($parts[$key]) ) {
                    $url_parts[] = $parts[$key];
                }
            }

            $url = implode('/', $url_parts);
        }
        else {
            foreach ($keys as $key) {
                if ( is_null($parts[$key]) ) {
                    unset($parts[$key]);
                }
            }

            $url = sprintf('?%s', http_build_query($parts));
        }

        return $url;
    }

    static public function url($handler=null, $action=null, $params=array()) {
        # returns /handler/action[/param1/param2...]
        $url_parts = array($handler, $action);

        foreach ( $params as $param ) {
            $url_parts[] = $param;
        }

        $url = implode('/', $url_parts);

        if ( substr($url, 0, 1) !== '/' ) {
            $url = '/' . $url;
        }

        return $url;
    }
}
