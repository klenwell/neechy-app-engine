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


class NeechyModelTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        #$this->neechy = NeechyModelFixture::init();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
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

    public function testSaveWithInvalidField() {
        return $this->markTestIncomplete('TODO: requires fixture');

        $neechy = NeechyModel::init(array(
            'neech' => 'foo',
            'invalid' => 'bar'
        ));
        $this->setExpectedException('PDOException');
        $query = $neechy->save();
    }

    public function testSave() {
        return $this->markTestIncomplete('TODO: requires fixture');

        $neechy = NeechyModel::init(array(
            'neech' => 'foo',
        ));
        $query = $neechy->save();
        $this->assertEquals(1, $query->rowCount());
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
        $this->assertInstanceOf('PDO', $model->pdo);
        $this->assertEquals('neeches', $model->table);
    }
}
