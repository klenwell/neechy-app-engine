<?php
/**
 * core/models/page.php
 *
 * Neechy Page model class.
 *
 */
require_once('../core/models/base.php');


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
	KEY idx_saved_at (saved_at),
) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE={{ engine }}
MYSQL;

    /*
     * Static Methods
     */
    public static function find_by_tag($tag) {
        $sql = "SELECT * FROM pages WHERE tag = ? ORDER BY saved_at DESC LIMIT 1";

        $pdo = WikkaRegistry::connect_to_db();
        $query = $pdo->prepare($sql);
        $query->execute(array($tag));
        $page_exists = $query->fetch(PDO::FETCH_ASSOC);

        $page = new PageModel();

        if ( $page_exists ) {
            $page->fields = $result;
        }
        else {
            $page->fields['tag'] = $tag;
        }

        # Load original version

        # Load ACLs

        return $page;
    }

    /*
     * Instance Methods
     */
    public function save() {
        $sql_f = 'INSERT INTO pages (%s, saved_at) VALUES (%s, NOW())';

        # Use database time for saved_at
        if ( isset($this->fields['saved_at']) ) {
            unset($this->fields['saved_at']);
        }

        $sql = sprintf($sql_f,
            implode(', ', array_keys($this->fields)),
            implode(', ', array_fill(0, count($this->fields), '?'))
        );

        $query = $this->pdo->prepare($sql);
        $query->execute(array_values($this->fields));
        return $query;
    }

    public function exists() {
        return !(is_null($this->field('id')));
    }
}
