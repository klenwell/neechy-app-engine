<?php
/**
 * core/neechy/security.php
 *
 * Neechy Security class
 *
 */
require_once('../core/neechy/config.php');
require_once('../core/neechy/errors.php');


class NeechySecurity {

    #
    # Public Static Methods
    #
    static public function start_session() {
        $session_name = md5(sprintf('%s.neechy', NeechyConfig::get('title', 'neechy')));
        session_name($session_name);
        session_start();
        return null;
    }

    static public function prevent_csrf() {
        self::set_csrf_token_if_not_set();
        self::authenticate_csrf_token();
        return true;
    }

    static public function hash_password($password) {
        # Based on https://gist.github.com/dzuelke/972386
        # See also http://stackoverflow.com/questions/4795385
        $algorithm = '2a';  # bcrypt
        $workload_factor = 12;
        $salt_prefix = sprintf('$%s$%02d$', $algorithm, $workload_factor);

        $blowfish_salt = NeechySecurity::random_hex(44);
        $hash = crypt($password, $salt_prefix.$blowfish_salt);
        return $hash;
    }

    static public function verify_password($input, $stored_hash) {
        $hash = crypt($input, $stored_hash);
        return $hash === $stored_hash;
    }

    static public function random_hex($length=32) {
        return bin2hex(openssl_random_pseudo_bytes($length/2));
    }

    #
    # Private Static Methods
    #
    static private function set_csrf_token_if_not_set() {
        if ( ! isset($_SESSION['csrf_token']) ) {
            $_SESSION['csrf_token'] = NeechySecurity::random_hex(40);
        }
        return $_SESSION['csrf_token'];
    }

    static private function authenticate_csrf_token() {
        if ( ! $_POST ) {
            return true;
        }
        else {
            $posted_token = (isset($_POST['csrf_token'])) ? $_POST['csrf_token'] : null;
            $session_token = $_SESSION['csrf_token'];

            if ( ! $posted_token ) {
                throw new NeechyCsrfError('Authentication failed: No CSRF token');
            }
            elseif ( $posted_token != $session_token ) {
                throw new NeechyCsrfError('Authentication failed: CSRF token mismatch');
            }
            else {
                return true;
            }
        }
    }
}
