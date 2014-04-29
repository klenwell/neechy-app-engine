<?php
/**
 * test/models/NeechyModelTest.php
 *
 * Usage (run from root dir):
 * > phpunit --bootstrap test/bootstrap.php models/NeechyModelTest
 *
 */
require_once('../core/models/base.php');


class NeechyModelTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
    }

    public function tearDown() {
    }

    /**
     * Tests
     */
    public function testFindById() {
        $this->markTestIncomplete('TODO');
    }

    public function testFindByColumnValue() {
        $this->markTestIncomplete('TODO');
    }

    public function testSave() {
        $this->markTestIncomplete('TODO');
    }

    public function testAll() {
        $this->markTestIncomplete('TODO');
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
        $this->assertEquals('neeches', $model->table);
    }
}
