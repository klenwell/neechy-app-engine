<?php
/**
 * core/neechy/database.php
 *
 * Neechy database module
 *
 */
require_once('../core/neechy/config.php');


class NeechyDatabaseError extends NeechyError {}


class NeechyDatabase {
    #
    # Properties
    #
    static private $models = array('Page', 'User');
    static private $pdo = null;

    /*
     * API
     */
    static public function connect_to_database_host() {
        self::verify_host_is_configured();

        # Do not cache connection since not selecting database.
        $dsn = sprintf('mysql:host=%s', NeechyConfig::get('mysql_host'));
        $pdo = new PDO($dsn,
            NeechyConfig::get('mysql_user'),
            NeechyConfig::get('mysql_password')
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    static public function connect_to_db() {
        self::verify_host_is_configured();

        if ( ! is_null(self::$pdo) ) {
            return self::$pdo;
        }
        else {
            $dsn_format = 'mysql:%s;dbname=%s';
            $db_host = NeechyConfig::get('mysql_host');

            if ( strpos($db_host, 'unix_socket') === 0 ) {
                $dsn_host = $db_host;
            }
            else {
                $dsn_host = sprintf('host=%s', $db_host);
            }

            $dsn = sprintf($dsn_format,
                           $dsn_host,
                           NeechyConfig::get('mysql_database'));
            self::$pdo = new PDO($dsn,
                                 NeechyConfig::get('mysql_user'),
                                 NeechyConfig::get('mysql_password'));
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$pdo;
        }
    }

    static public function disconnect_from_db() {
        self::$pdo = null;
    }

    static public function reconnect_to_db() {
        self::disconnect_from_db();
        self::connect_to_db();
    }

    static public function database_exists($db_name) {
        $sql = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?';

        $pdo = self::connect_to_database_host();
        $query = $pdo->prepare($sql);
        $query->execute(array($db_name));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        return ((bool) $row);
    }

    static public function create_database() {
        $database = NeechyConfig::get('mysql_database');
        $pdo = self::connect_to_database_host();
        $pdo->exec(sprintf('CREATE DATABASE `%s`', $database));
        return $pdo;
    }

    static public function destroy_database() {
        $database = NeechyConfig::get('mysql_database');
        $pdo = self::connect_to_database_host();
        $pdo->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $database));
        return $pdo;
    }

    static public function create_model_tables() {
        $created_tables = array();

        foreach ( self::$models as $model_name ) {
            $model = new $model_name();
            $model_class = get_class($model);

            if ( ! $model_class::table_exists() ) {
                $created_tables[] = $model_class::create_table_if_not_exists();
            }
        }

        return $created_tables;
    }

    static public function drop_model_tables() {
        $dropped_tables = array();

        foreach ( self::$models as $model_name ) {
            $model = new $model_name();
            $model_class = get_class($model);
            $dropped_tables[] = $model_class::drop_table_if_exists();
        }

        return $dropped_tables;
    }

    static public function core_model_classes() {
        $model_classes = array();

        foreach ( self::$models as $model_name ) {
            $model = new $model_name();
            $model_classes[] = get_class($model);
        }

        return $model_classes;
    }

    static public function connection_status() {
        $db = self::connect_to_db();
        return $db->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    }

    static public function reset() {
        # Require these here to avoid circular dependency error.
        require_once('../app/models/user.php');
        require_once('../app/models/page.php');

        self::drop_model_tables();
        self::create_model_tables();
        AppUser::create_on_install();
        AppPage::create_on_install();
    }

    #
    # Private class function
    #
    static protected function verify_host_is_configured() {
        $host = NeechyConfig::get('mysql_host');

        if ( is_null($host) || $host == 'NULL' ) {
            throw new NeechyDatabaseError('mysql_host set to null');
        }
        else {
            return true;
        }
    }
}
