<?php
/**
 * core/models/user.php
 *
 * Neechy User model class.
 *
 */
require_once('../core/models/base.php');


class User extends NeechyModel {

    protected static $schema = <<<MYSQL
CREATE TABLE users (
    id int(11) NOT NULL auto_increment,
    name varchar(255) NOT NULL default '',
    email varchar(255) NOT NULL default '',
    password varchar(255) NOT NULL default '',
    status varchar(16) NOT NULL default '',
    challenge varchar(8) default '',

    theme varchar(64) default '',
    show_comments enum('Y','N') NOT NULL default 'N',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME default NULL,

    PRIMARY KEY (id),
    KEY idx_name (name),
    KEY idx_created_at (created_at)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE={{ engine }}
MYSQL;

    #
    # Constants
    #

    /*
     * Constructor
     */

    /*
     * Static Methods
     */
    public static function find_by_name($name) {
        $sql = "SELECT * FROM users WHERE name = ? ORDER BY updated_at DESC LIMIT 1";

        $pdo = NeechyDatabase::connect_to_db();
        $query = $pdo->prepare($sql);
        $query->execute(array($name));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ( $row ) {
            $user = new User($row);
        }
        else {
            $user = new User(array('name' => $name));
        }

        return $user;
    }

    public static function is_logged_in() {
        return isset($_SESSION['logged-in']);
    }

    public static function logout() {
        unset($_SESSION['logged-in']);
        return null;
    }

    public static function is_admin() {
    }

    /*
     * Instance Methods
     */
    public function save() {
        $sql_f = 'INSERT INTO users (%s, updated_at) VALUES (%s, NOW())';

        # Use database values
        $this->un_set('id');
        $this->un_set('updated_at');

        $sql = sprintf($sql_f,
            implode(', ', array_keys($this->fields)),
            implode(', ', array_fill(0, count($this->fields), '?'))
        );

        $query = $this->pdo->prepare($sql);
        $query->execute(array_values($this->fields));
        return $query;
    }

    public function is_new() {
        return is_null($this->field('id'));
    }

    public function exists() {
        return !($this->is_new());
    }

    public function url($handler=NULL, $action=NULL, $params=array()) {
        return NeechyPath::url($this->field('name'), $handler, $action, $params);
    }

    #
    # Auth Methods
    #
    public function login() {
        $_SESSION['logged-in'] = microtime(1);
        return null;
    }
}
