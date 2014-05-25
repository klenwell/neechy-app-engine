<?php

require_once('../test/fixtures/neechy.php');


class PageFixture extends NeechyFixture {

    static protected $model_class = 'Page';

    static public $data = array(
        array(
            'title' => 'NeechyPage',
            'slug' => 'neechypage',
            'editor' => 'Anonymous',
            'body' => 'That which does not kill us makes us lunch.',
            'note' => 'version 1'
        ),
        array(
            'title' => 'NeechyPage',
            'slug' => 'neechypage',
            'editor' => 'Anonymous',
            'body' => 'That which does not kill us makes us manlier.',
            'note' => 'version 2'
        ),
        array(
            'title' => 'NeechyPage',
            'slug' => 'neechypage',
            'editor' => 'Anonymous',
            'body' => 'That which does not kill us makes us stronger.',
            'note' => 'version 3'
        ),
    );
}
