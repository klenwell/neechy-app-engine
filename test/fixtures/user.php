<?php

require_once('../test/fixtures/neechy.php');


class UserFixture extends NeechyFixture {

    static protected $model_class = 'User';

    static public $data = array(
        array(
            'name' => 'NeechyUser',
            'email' => 'nuser@neechy.org'
        )
    );
}
