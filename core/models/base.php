<?php
/**
 * core/models/base.php
 *
 * Base Neechy model class.
 *
 * Provides basic interface for models. Each model should be associated with
 * a table and a schema property holding the sql for the table. It also
 * establishes a single PDO connection for all model instances.
 *
 *
 * USAGE
 *  require_once('models/base.php');
 *
 *  class Page extends NeechyModel {
 *  }
 *
 */
require_once('../core/libs/constants.php');
#require_once('../core/libs/database.php');



class NeechyModel {

    /*
     * Static Properties
     * (These are just a sample and should be overridden in base class)
     */
    protected static $schema = <<<MYSQL
CREATE TABLE neeches (
	id int(10) unsigned NOT NULL auto_increment,
	neech varchar(75) NOT NULL default '',
	PRIMARY KEY  (id),
	KEY idx_neech (neech)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE={{ engine }}
MYSQL;

    /*
     * Properties
     */
    public $fields = array();
    public $table = '';
    private $pdo = null;

    /*
     * Constructor
     */
    public function __construct() {
        $this->table = $this->extract_table_name();
        #$this->pdo = NeechyDatabase::connect_to_db();
    }

    /*
     * Static Methods
     */
    static public function init($fields=array()) {
        $class = get_called_class();
        $instance = new $class();
        $instance->fields = $fields;
        return $instance;
    }

    static public function get_schema() {
        $schema = str_replace('{{ engine }}', MYSQL_ENGINE, static::$schema);
        return $schema;
    }

    static public function all() {
        $sql = sprintf('SELECT * FROM %s', $this->table);
        return $this->pdo->query($sql);
    }

    /*
     * Public Methods
     */
    public function field($name, $default=NULL) {
        if ( isset($this->fields[$name]) ) {
            return $this->fields[$name];
        }
        else {
            return $default;
        }
    }

    public function save() {
        $sql_f = 'INSERT INTO %s (%s) VALUES (%s)';
        $sql = sprintf($sql_f,
            $this->get_table(),
            implode(', ', array_keys($this->fields)),
            implode(', ', array_fill(0, count($this->fields), '?'))
        );

        $query = $this->pdo->prepare($sql);
        $query->execute(array_values($this->fields));
        return $query;
    }

    public function find_by_column_value($column, $value) {
        $sql = sprintf('SELECT * FROM %s WHERE %s = ?', $this->table, $column);
        $query = $this->pdo->prepare($sql);
        return $query->execute(array($value));
    }

    public function find_by_id($id) {
        return $this->find_by_column_value('id', $id);
    }

    /*
     * Private Method
     */
    private function extract_table_name() {
        $regex = '/CREATE TABLE([^\(]+)\(/';
        $matches = array();
        $matched = preg_match($regex, self::$schema, $matches);
        return trim($matches[1]);
    }
}
