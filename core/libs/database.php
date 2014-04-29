<?php
/**
 * core/libs/database.php
 *
 * Neechy database module
 *
 */
require_once('../core/libs/config.php');


class NeechyDatabase {
    #
    # Properties
    #
    static private $pdo = null;

    /*
     * API
     */
    static public function connect_to_db() {
        if ( ! is_null(self::$pdo) ) {
            return self::$pdo;
        }
        else {
            $dsn = sprintf('mysql:host=%s;dbname=%s',
                NeechyConfig::get('mysql_host'),
                NeechyConfig::get('mysql_database')
            );
            self::$pdo = new PDO($dsn,
                NeechyConfig::get('mysql_user'),
                NeechyConfig::get('mysql_password')
            );
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$pdo;
        }
    }

    static public function disconnect_from_db() {
        self::$pdo = null;
    }
}
