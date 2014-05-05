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
 *  require_once('core/models/base.php');
 *
 *  class Page extends NeechyModel {
 *  }
 *
 */
require_once('../core/neechy/constants.php');
require_once('../core/neechy/database.php');



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
    public $pdo = null;

    /*
     * Constructor
     */
    public function __construct($fields=array()) {
        $this->table = self::extract_table_name();
        $this->pdo = NeechyDatabase::connect_to_db();
        $this->fields = $fields;
    }

    /*
     * Static Methods
     */
    static public function init($fields=array()) {
        $class = get_called_class();
        $instance = new $class($fields);
        return $instance;
    }

    static public function get_schema() {
        $schema = str_replace('{{ engine }}', MYSQL_ENGINE, static::$schema);
        return $schema;
    }

    static public function table_exists() {
        # http://stackoverflow.com/a/14355475/1093087
        $sql = sprintf('SELECT 1 FROM %s LIMIT 1', self::extract_table_name());
        $pdo = NeechyDatabase::connect_to_db();

        try {
            $found = $pdo->query($sql);
        } catch (PDOException $e) {
            return FALSE;
        }

        return $found !== FALSE;
    }

    static public function create_table_if_not_exists() {
        if ( ! self::table_exists() ) {
            $model_class = get_called_class();
            $model = new $model_class();
            $model->pdo->exec($model_class::get_schema());
        }
    }

    static public function all() {
        $sql = sprintf('SELECT * FROM %s', self::extract_table_name());
        $pdo = NeechyDatabase::connect_to_db();
        $statement = $pdo->query($sql);
        return $statement->fetchAll();
    }

    /*
     * Public Methods
     */
    public function set($field, $value) {
        $this->fields[$field] = $value;
    }

    public function un_set($field) {
        if ( isset($this->fields[$field]) ) {
            unset($this->fields[$field]);
        }
    }

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
            $this->table,
            implode(', ', array_keys($this->fields)),
            implode(', ', array_fill(0, count($this->fields), '?'))
        );

        $query = $this->pdo->prepare($sql);
        $query->execute(array_values($this->fields));
        return $query;
    }

    public function find_by_column_value($column, $value) {
        $records = array();
        $ModelClass = get_class($this);

        $sql = sprintf('SELECT * FROM %s WHERE %s = ?', $this->table, $column);
        $query = $this->pdo->prepare($sql);
        $query->execute(array($value));
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ( $rows as $row ) {
            $records[] = new $ModelClass($row);
        }

        return $records;
    }

    public function find_by_id($id) {
        $records = $this->find_by_column_value('id', $id);

        if ( $records ) {
            return $records[0];
        }
        else {
            return NULL;
        }
    }

    /*
     * Private Method
     */
    private static function extract_table_name() {
        $regex = '/CREATE TABLE([^\(]+)\(/';
        $matches = array();
        $matched = preg_match($regex, static::$schema, $matches);
        return trim($matches[1]);
    }
}
