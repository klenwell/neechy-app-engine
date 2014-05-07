<?php
/**
 * test/neechy/ErrorsTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/ErrorsTest
 *
 */
require_once('../core/neechy/errors.php');


class NeechyErrorsTest extends PHPUnit_Framework_TestCase {

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
    public function testNeechyWebServiceError() {
        try {
            throw new NeechyWebServiceError('unit test');
        }
        catch (NeechyWebServiceError $e) {
            $this->assertEquals('unit test', $e->getMessage());
        }

        try {
            throw new NeechyWebServiceError('unit test');
        }
        catch (NeechyError $e) {
            $this->assertInstanceOf('NeechyError', $e);
        }
    }

    public function testNeechyError() {
        try {
            throw new NeechyError('unit test');
        }
        catch (NeechyError $e) {
            $this->assertEquals('NeechyError: unit test', trim((string) $e));
            $this->assertEquals('unit test', $e->getMessage());
        }
    }

    public function testInstantiates() {
        $error = new NeechyError('instantiating normally');
        $this->assertInstanceOf('NeechyError', $error);
    }
}
