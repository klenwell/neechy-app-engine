<?php
/**
 * test/models/NeechyModelTest.php
 *
 * Usage (run from root dir):
 * > phpunit --bootstrap test/bootstrap.php models/NeechyModelTest
 *
 */
require_once('../core/models/base.php');
require_once('../test/helper.php');
require_once('../test/fixtures/neechy.php');


class NeechyModelTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        NeechyFixture::init();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
    }

    /**
     * Tests
     */
    public function testCount() {
        $neechy = new NeechyModel();
        $this->assertEquals(3, $neechy->count());
    }

    public function testFindById() {
        $neechy = new NeechyModel();
        $foo_rows = $neechy->find_by_column_value('neech', 'foo');
        $record = $neechy->find_by_id($foo_rows[0]->field('id'));
        $this->assertEquals($foo_rows[0]->field('id'), $record->field('id'));
    }

    public function testFindByColumnValue() {
        $neechy = new NeechyModel();
        $rows = $neechy->find_by_column_value('neech', 'foo');
        $this->assertCount(1, $rows);
        $this->assertEquals('foo', $rows[0]->field('neech'));

        $rows = $neechy->find_by_column_value('neech', 'value not in table');
        $this->assertCount(0, $rows);
    }

    public function testSaveWithInvalidField() {
        $neechy = NeechyModel::init(array(
            'neech' => 'testSaveWithInvalidField',
            'invalid' => 'field'
        ));
        $this->setExpectedException('PDOException');
        $neechy->save();
    }

    public function testInsert() {
        $neechy = NeechyModel::init(array('neech' => 'testInsert'));
        $neechy->insert();
        $this->assertEquals(1, $neechy->rows_affected);

        $rows = $neechy->find_by_column_value('neech', 'testInsert');
        $this->assertEquals(1, count($rows));
        $this->assertEquals('testInsert', $rows[0]->field('neech'));
    }

    public function testUpdate() {
        $neechy = NeechyModel::init(array('neech' => 'testInsert'));
        $neechy->insert();
        $this->assertEquals(1, $neechy->rows_affected);

        $neechy->set('neech', 'testUpdate');
        $neechy->update();
        $this->assertEquals(1, $neechy->rows_affected);

        $rows = $neechy->find_by_column_value('neech', 'testUpdate');
        $this->assertEquals(1, count($rows));
        $this->assertEquals('testUpdate', $rows[0]->field('neech'));
    }

    public function testSave() {
        # First save: insert
        $neechy = NeechyModel::init(array('neech' => 'first save'));
        $neechy->save();

        $rows = $neechy->find_by_column_value('neech', 'first save');
        $saved_neechy = $rows[0];
        $this->assertEquals(1, count($rows));
        $this->assertEquals('first save', $saved_neechy->field('neech'));

        # Subsequent saves: update
        $neechy->set('neech', 'second save');
        $neechy->save();

        $rows = $neechy->find_by_column_value('neech', 'first save');
        $this->assertEquals(0, count($rows));

        $rows = $neechy->find_by_column_value('neech', 'second save');
        $updated_neechy = $rows[0];
        $this->assertEquals(1, count($rows));
        $this->assertEquals('second save', $updated_neechy->field('neech'));

        $this->assertEquals($saved_neechy->id(), $updated_neechy->id());
    }

    public function testAll() {
        $rows = NeechyModel::all();
        $this->assertCount(3, $rows);
    }

    public function testInitAndField() {
        $neechy = NeechyModel::init(array(
            'neech' => 'foo',
        ));
        $this->assertInstanceOf('NeechyModel', $neechy);
        $this->assertEquals('foo', $neechy->field('neech'));
        $this->assertNull($neechy->field('niche'));
        $this->assertEquals('default', $neechy->field('niche', 'default'));
    }

    public function testTableSchema() {
        $neechy = new NeechyModel();
        $schema = trim($neechy->get_schema());
        $this->assertStringStartsWith('CREATE TABLE neeches', $schema);
        $this->assertStringEndsWith('ENGINE=MyISAM', $schema);
    }

    public function testInstantiates() {
        $model = new NeechyModel();
        $this->assertInstanceOf('NeechyModel', $model);
        $this->assertInstanceOf('PDO', $model->pdo);
        $this->assertEquals('neeches', $model->table);
    }
}
