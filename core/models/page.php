<?php
/**
 * core/models/page.php
 *
 * Neechy Page model class.
 *
 * Slug is a normalized version of the title. It is used to minimize minor variations
 * of page titles.
 *
 */
require_once('../core/models/base.php');
require_once('../core/models/user.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/helper.php');
require_once('../core/neechy/formatter.php');


class Page extends NeechyModel {

    const MAX_BODY_LENGTH = 1000;

    protected static $schema = <<<MYSQL
CREATE TABLE pages (
    id int(11) NOT NULL auto_increment,
    primogenitor_id int(11) default NULL,
    editor varchar(255) NOT NULL default '',
    slug varchar(255) NOT NULL default '',
    title varchar(255) NOT NULL default '',
    body mediumtext NOT NULL,
    note varchar(255) NOT NULL default '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_primogenitor_id (primogenitor_id),
    KEY idx_editor (editor),
    KEY idx_slug (slug),
    FULLTEXT KEY body (body),
    KEY idx_created_at (created_at)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE={{ engine }}
MYSQL;

    protected static $immutable_fields = array('id', 'created_at');

    public $primogenitor = NULL;
    public $editor = NULL;
    public $edits = array();
    public $validation_errors = array();

    #
    # Constructor
    #
    public function __construct($fields=array()) {
        parent::__construct($fields);

        # Set primogenitor
        if ( $this->field('primogenitor_id') ) {
            $this->primogenitor = $this->find_by_id($this->field('primogenitor_id'));
        }

        if ( $this->field('editor') ) {
            $this->editor = User::find_by_name($this->field('editor'));
        }
    }

    #
    # Static Methods
    #
    public static function find_by_slug($slug) {
        $sql = "SELECT * FROM pages WHERE slug = ? ORDER BY created_at DESC LIMIT 1";

        $pdo = NeechyDatabase::connect_to_db();
        $query = $pdo->prepare($sql);
        $query->execute(array($slug));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ( $row ) {
            $page = new Page($row);
        }
        else {
            $page = new Page(array('slug' => $slug));
        }

        return $page;
    }

    public static function find_by_title($title) {
        $slug = self::title_to_slug($title);
        $page = self::find_by_slug($slug);
        $page->set('title', $title);
        return $page;
    }

    static private function title_to_slug($title) {
        return preg_replace('/[_+\-\s]/', '', strtolower($title));
    }

    #
    # Public Save Methods
    #
    public function insert() {
        $sql_f = 'INSERT INTO pages (%s) VALUES (%s)';
        $this->filter_immutable_fields();

        # Set primogenitor
        $primogenitor = $this->find_primogenitor_by_title($this->field('title'));
        if ( $primogenitor ) {
            $this->set('primogenitor_id', $primogenitor->field('id'));
        }

        # Set editor
        $editor = User::current('name');
        if ( $editor ) {
            $this->set('editor', $editor);
        }

        $sql = sprintf($sql_f,
            implode(', ', array_keys($this->fields)),
            implode(', ', array_fill(0, count($this->fields), '?'))
        );

        $query = $this->pdo->prepare($sql);
        $query->execute(array_values($this->fields));
        $this->rows_affected = $query->rowCount();
        $this->set('id', $this->pdo->lastInsertId());
        return $this;
    }

    public function save() {
        # There is no updates. All changes to pages are recorded.
        return $this->insert();
    }

    #
    # Validation Methods
    #
    public function is_valid() {
        $this->validation_errors = array();
        $this->validate_body();
        return count($this->validation_errors) < 1;
    }

    public function validate_body() {
        # empty pre-PHP 5.5 needs a variable.
        # See http://stackoverflow.com/a/2173318/1093087.
        $body = $this->field('body');

        if ( empty($body) ) {
            $this->validation_errors['body'] = 'Body field required.';
        }
        elseif ( strlen($body) > self::MAX_BODY_LENGTH ) {
            $this->validation_errors['body'] =
                sprintf('Page length can be no longer than %s characters. Please shorten.',
                        self::MAX_BODY_LENGTH);
        }
    }

    public function error_message() {
        $messages = array();

        foreach ( $this->validation_errors as $field => $error ) {
            $messages[] = sprintf('<p>%s</p>', $error);
        }

        return implode("\n", $messages);
    }

    #
    # Public Find Methods
    #
    public function find_primogenitor_by_title($title) {
        $sql = 'SELECT * FROM pages WHERE slug = ? ORDER BY id ASC LIMIT 1';
        $query = $this->pdo->prepare($sql);
        $query->execute(array(self::title_to_slug($title)));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ( $row ) {
            return new Page($row);
        }
        else {
            return null;
        }
    }

    public function load_history($limit=0) {
        // Loads page history and (1) assigns as array of Page instances to edits
        // and (2) return array of associate array data from table.
        $sql_f = 'SELECT * FROM pages WHERE slug = ? ORDER BY id DESC%s';

        # Set limit
        if ( $limit > 0 ) {
            $limit_clause = sprintf(' LIMIT %d', $limit);
        }
        else {
            $limit_clause = '';
        }

        $sql = sprintf($sql_f, $limit_clause);
        $query = $this->pdo->prepare($sql);
        $query->execute(array($this->field('slug')));
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        $edits = array();
        $augmented_rows = array();
        foreach ( $rows as $row ) {
            $page = new Page($row);
            $edits[$row['id']] = $page;
            $row['history_url'] = $page->historical_url();
            $augmented_rows[] = $row;
        }
        $this->edits = $edits;

        return $augmented_rows;
    }

    #
    # Public Attribute Methods
    #
    public function url($handler='page', $options=array()) {
        return NeechyPath::url($handler, $this->field('slug'), $options);
    }

    public function historical_url() {
        return sprintf('/history/%s/%s', $this->field('slug'), $this->field('id'));
    }

    public function editor_url() {
        return sprintf('/editor/%s', $this->field('slug'));
    }

    public function editor_link() {
        if ( ! $this->editor ) {
            return 'N/A';
        }
        else {
            $editor_name = $this->editor->field('name');
            return NeechyHelper::handler_link($editor_name, 'page', $editor_name);
        }
    }

    public function get_title($default='Page') {
        return $this->field('title', $this->field('slug', $default));
    }

    public function title() {
        return NeechyTemplater::titleize_camel_case($this->get_title());
    }

    public function body_to_html() {
        # Returns body as html.
        $formatter = new NeechyFormatter();
        return $formatter->wml_to_html($this->field('body'));
    }
}
