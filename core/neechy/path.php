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
}
