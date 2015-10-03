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
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME default NULL,
	PRIMARY KEY  (id),
	KEY idx_neech (neech)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE={{ engine }}
MYSQL;

    protected static $immutable_fields = array('id', 'created_at', 'updated_at');

    /*
     * Properties
     */
    public $fields = array();
    public $table = '';
    public $pdo = null;
    public $rows_affected = 0;

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
            return $model->table;
        }
        else {
            return null;
        }
    }

    static public function all() {
        $sql = sprintf('SELECT * FROM %s', self::extract_table_name());
        $pdo = NeechyDatabase::connect_to_db();
        $statement = $pdo->query($sql);
        return $statement->fetchAll();
    }

    #
    # Public Instance Methods
    #
    public function is_new() {
        return is_null($this->field('id'));
    }

    public function exists() {
        return !($this->is_new());
    }

    public function to_json() {
        return json_encode($this->fields);
    }

    #
    # Public Field Methods
    #
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

    public function id() {
        return $this->field('id');
    }

    #
    # Public Save Methods
    #
    public function insert() {
        $sql_f = 'INSERT INTO %s (%s) VALUES (%s)';
        $this->filter_immutable_fields();

        $sql = sprintf($sql_f,
            $this->table,
            implode(', ', array_keys($this->fields)),
            implode(', ', array_fill(0, count($this->fields), '?'))
        );

        $this->rows_affected = 0;
        $query = $this->pdo->prepare($sql);
        $query->execute(array_values($this->fields));
        $this->rows_affected = $query->rowCount();
        $this->set('id', $this->pdo->lastInsertId());
        return $this;
    }

    public function update() {
        $sql_f = 'UPDATE %s SET %s WHERE id = ?';
        $mutable_fields = $this->mutable_fields();

        # Build SET clause
        $set_pairs = array();
        foreach ( $mutable_fields as $key ) {
            $set_pairs[] = sprintf('%s = ?', $key);
        }
        $set_pairs[] = 'updated_at = NOW()';

        # Build SQL query
        $sql = sprintf($sql_f,
            $this->table,
            implode(', ', $set_pairs)
        );

        # Build value array
        $values = array();
        foreach ($mutable_fields as $field) {
            $values[] = $this->field($field);
        }
        $values[] = $this->field('id');

        # Execute
        $this->rows_affected = 0;
        $query = $this->pdo->prepare($sql);
        $query->execute($values);
        $this->rows_affected = $query->rowCount();
        return $this;
    }

    public function save() {
        if ( $this->is_new() ) {
            $this->insert();
        }
        else {
            $this->update();
        }

        return $this->rows_affected > 0;
    }

    #
    # Public Select Methods
    #
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

    #
    # Protected Methods
    #
    protected function mutable_fields() {
        return array_diff(array_keys($this->fields), self::$immutable_fields);
    }

    protected function filter_immutable_fields() {
        $filtered_fields = array();

        foreach ($this->mutable_fields() as $field) {
            $filtered_fields[$field] = $this->field($field);
        }

        $this->fields = $filtered_fields;
        return $this;
    }

    #
    # Private Methods
    #
    private static function extract_table_name() {
        $regex = '/CREATE TABLE([^\(]+)\(/';
        $matches = array();
        $matched = preg_match($regex, static::$schema, $matches);
        return trim($matches[1]);
    }
}
