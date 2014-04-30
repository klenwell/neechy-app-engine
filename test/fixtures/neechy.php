<?php

require_once('../core/models/base.php');


class NeechyFixture {

    static private $model_class = 'NeechyModel';
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
        $model = new self::$model_class();
        $model_class = get_class($model);
        $model->pdo->exec($model_class::get_schema());
    }

    static protected function init_fixture_data() {
        $model = new self::$model_class();
        foreach ( self::$data as $record ) {
            $model->fields = $record;
            $model->save();
        }
    }
}
