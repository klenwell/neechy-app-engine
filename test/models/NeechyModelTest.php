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
    public function testFindById() {
        $neechy = new NeechyModel();
        $foo_rows = $neechy->find_by_column_value('neech', 'foo');
        $rows = $neechy->find_by_id($foo_rows[0]['id']);
        $this->assertEquals($foo_rows[0]['id'], $rows[0]['id']);
    }

    public function testFindByColumnValue() {
        $neechy = new NeechyModel();
        $rows = $neechy->find_by_column_value('neech', 'foo');
        $this->assertCount(1, $rows);
        $this->assertEquals('foo', $rows[0]['neech']);

        $rows = $neechy->find_by_column_value('neech', 'value not in table');
        $this->assertCount(0, $rows);
    }

    public function testSaveWithInvalidField() {
        $neechy = NeechyModel::init(array(
            'neech' => 'testSaveWithInvalidField',
            'invalid' => 'field'
        ));
        $this->setExpectedException('PDOException');
        $query = $neechy->save();
    }

    public function testSave() {
        $neechy = NeechyModel::init(array(
            'neech' => 'testSave',
        ));
        $query = $neechy->save();
        $this->assertEquals(1, $query->rowCount());
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
