<?php
/**
 * core/models/page.php
 *
 * Neechy Page model class.
 *
 */
require_once('../core/models/base.php');
require_once('../core/neechy/path.php');


class Page extends NeechyModel {

    protected static $schema = <<<MYSQL
CREATE TABLE pages (
	id int(11) NOT NULL auto_increment,
    primogenitor_id int(11) default NULL,
    editor varchar(255) NOT NULL default '',
	tag varchar(255) NOT NULL default '',
    body mediumtext NOT NULL,
    note varchar(255) NOT NULL default '',
    saved_at DATETIME default NULL,
	PRIMARY KEY (id),
    KEY idx_primogenitor_id (primogenitor_id),
	KEY idx_editor (editor),
	KEY idx_tag (tag),
	FULLTEXT KEY body (body),
	KEY idx_saved_at (saved_at)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE={{ engine }}
MYSQL;

    public $primogenitor = NULL;
	public $editor = NULL;

    /*
     * Constructor
     */
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

    /*
     * Static Methods
     */
    public static function find_by_tag($tag) {
        $sql = "SELECT * FROM pages WHERE tag = ? ORDER BY saved_at DESC LIMIT 1";

        $pdo = NeechyDatabase::connect_to_db();
        $query = $pdo->prepare($sql);
        $query->execute(array($tag));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ( $row ) {
            $page = new Page($row);
        }
        else {
            $page = new Page(array('tag' => $tag));
        }

        return $page;
    }

    /*
     * Instance Methods
     */
    public function save() {
        $sql_f = 'INSERT INTO pages (%s, saved_at) VALUES (%s, NOW())';

        # Set primogenitor
        if ( $primogenitor = $this->find_primogenitor_by_tag($this->field('tag')) ) {
            $this->set('primogenitor_id', $primogenitor->field('id'));
        }

        # Use database time for saved_at
        $this->un_set('id');
        $this->un_set('saved_at');

        $sql = sprintf($sql_f,
            implode(', ', array_keys($this->fields)),
            implode(', ', array_fill(0, count($this->fields), '?'))
        );

        $query = $this->pdo->prepare($sql);
        $query->execute(array_values($this->fields));
        return $query;
    }

    public function find_primogenitor_by_tag($tag) {
        $sql = 'SELECT * FROM pages WHERE tag = ? ORDER BY id ASC LIMIT 1';
        $query = $this->pdo->prepare($sql);
        $query->execute(array($tag));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ( $row ) {
            return new Page($row);
        }
        else {
            return NULL;
        }
    }

    public function is_new() {
        return is_null($this->field('id'));
    }

	public function url($handler=NULL, $action=NULL, $params=array()) {
		return NeechyPath::url($this->field('tag'), $handler, $action, $params);
	}

	public function editor_link() {
		if ( ! $this->editor ) {
			return 'N/A';
		}
		else {
			$t = NeechyTemplater::load();
			$editor_name = $this->editor->field('name');
			return $t->neechy_link($editor_name);
		}
	}
}
