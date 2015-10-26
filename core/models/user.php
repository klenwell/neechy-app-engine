<?php
/**
 * core/models/user.php
 *
 * Neechy User model class.
 *
 */
require_once('../core/models/base.php');
require_once('../core/models/page.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/templater.php');
require_once('../core/neechy/security.php');


class User extends NeechyModel {

    protected static $schema = <<<MYSQL
CREATE TABLE users (
    id int(11) NOT NULL auto_increment,
    name varchar(255) NOT NULL default '',
    email varchar(255) NOT NULL default '',
    password varchar(255) NOT NULL default '',
    status int(11) NOT NULL default 0,
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
    private static $STATUS_LEVELS = array('NEW'    => 1,
                                          'ADMIN'   => 2);

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

    public static function register($name, $email, $password, $level='NEW') {
        # Save user
        $user = User::find_by_name($name);
        $user->set('email', $email);
        $user->set_password($password);
        $user->set('status', self::$STATUS_LEVELS[$level]);
        $user->save();

        return $user;
    }

    public static function is_logged_in() {
        return isset($_SESSION['user']);
    }

    public static function is_admin() {
    }

    public static function current($field=null) {
        if ( ! User::is_logged_in() ) {
            return null;
        }
        elseif ( ! $field ) {
            return User::find_by_name($_SESSION['user']['name']);
        }
        else {
            return $_SESSION['user'][$field];
        }
    }

    public static function logout_current() {
        $current_user = User::current();
        if ( $current_user ) {
            $current_user->logout();
        }
    }

    /*
     * Instance Methods
     */
    public function url($handler=null, $params=array()) {
        $handler = ( is_null($handler) ) ? 'page' : $handler;
        return NeechyPath::url($handler, $this->field('name'), $params);
    }

    public function set_password($password) {
        $this->set('password', NeechySecurity::hash_password($password));
    }

    #
    # Auth Methods
    #
    public function login() {
        $_SESSION['user'] = array(
            'name' => $this->field('name'),
            'status' => $this->field('status'),
            'logged-in' => microtime(1)
        );
        return null;
    }

    public function logout() {
        unset($_SESSION['user']);
        return null;
    }
}
