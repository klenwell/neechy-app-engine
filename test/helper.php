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
        NeechyDatabase::disconnect_from_db();
        self::destroy_database();
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
        self::destroy_database();
        $pdo = self::create_database();
        $pdo->query(sprintf('USE %s', NeechyConfig::get('mysql_database')));
    }

    static public function destroy_session() {
        if ( session_id() ) {
            session_destroy();
            $_SESSION = array();
        }
    }

    static public function connect_to_database_host() {
        # Tests will hang if host is invalid
        $host = NeechyConfig::get('mysql_host');
        if ( is_null($host) || $host == 'NULL' ) {
            throw new Exception('mysql_host set to null');
        }

        $host = sprintf('mysql:host=%s', NeechyConfig::get('mysql_host'));
        $pdo = new PDO($host,
            NeechyConfig::get('mysql_user'),
            NeechyConfig::get('mysql_password')
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    static public function create_database() {
        $test_database = NeechyConfig::get('mysql_database');
        $pdo = self::connect_to_database_host();
        $pdo->exec(sprintf('CREATE DATABASE `%s`', $test_database));
        return $pdo;
    }

    static public function destroy_database() {
        $test_database = NeechyConfig::get('mysql_database');
        $pdo = self::connect_to_database_host();
        $pdo->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $test_database));
        return $pdo;
    }
}
