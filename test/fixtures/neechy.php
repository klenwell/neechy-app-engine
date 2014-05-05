<?php

require_once('../core/models/base.php');


class NeechyFixture {

    static protected $model_class = 'NeechyModel';
    static private $data = array(
        array(
            'neech' => 'foo'
        ),
        array(
            'neech' => 'bar'
        ),
        array(
            'neech' => 'baz'
        )
    );

    static public function init() {
        self::init_table();
        self::init_fixture_data();
    }

    static protected function init_table() {
        $model = new static::$model_class();
        $model_class = get_class($model);
        $model->pdo->exec($model_class::get_schema());
    }

    static protected function init_fixture_data() {
        $model = new static::$model_class();
        foreach ( static::$data as $record ) {
            $model->fields = $record;
            $model->save();
        }
    }
}
