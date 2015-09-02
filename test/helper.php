<?php

require_once('../core/neechy/constants.php');
require_once('../core/neechy/config.php');
require_once('../core/neechy/database.php');


class NeechyTestHelper {
    #
    # Constants
    #

    /*
     * API
     */
    static public function setUp() {
        self::init_config();
        self::init_server_env();
        self::init_database();
    }

    static public function tearDown() {
        $_SERVER = array();
        NeechyDatabase::destroy_database();
        NeechyDatabase::disconnect_from_db();
    }

    static public function init_config() {
        NeechyConfig::init();
    }

    static public function init_server_env() {
        $_SERVER = array(
            'SERVER_NAME'   => 'localhost',
            'SERVER_PORT'   => '80',
            'QUERY_STRING'  => 'page=HomePage',
            'REQUEST_URI'   => 'index.php?page=HomePage',
            'SCRIPT_NAME'   => 'index.php',
            'PHP_SELF'      => 'index.php',
            'REMOTE_ADDR'   => '127.0.0.1'
        );
        return $_SERVER;
    }

    static public function init_database() {
        NeechyDatabase::destroy_database();
        $pdo = NeechyDatabase::create_database();
        $pdo->query(sprintf('USE %s', NeechyConfig::get('mysql_database')));
    }

    static public function destroy_session() {
        if ( session_id() ) {
            session_destroy();
            $_SESSION = array();
        }
    }
}
