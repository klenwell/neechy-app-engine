<?php
/**
 * test/neechy/ValidatorTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/ValidatorTest
 *
 */
require_once('../core/neechy/request.php');
require_once('../core/neechy/validator.php');


class NeechyValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        $this->validator = new NeechyValidator(NULL);
    }

    public function tearDown() {
        $this->validator = NULL;
    }

    /**
     * Tests
     */
    public function testStringIsEmpty() {
        $this->assertEquals(true, $this->validator->string_is_empty((string) null));
        $this->assertEquals(false, $this->validator->string_is_empty((string) array()));
        $this->assertEquals(true, $this->validator->string_is_empty((string) false));
        $this->assertEquals(false, $this->validator->string_is_empty((string) 0));
        $this->assertEquals(true, $this->validator->string_is_empty(''));
        $this->assertEquals(false, $this->validator->string_is_empty('0'));
    }

    public function testIsEmpty() {
        $this->assertEquals(true, $this->validator->is_empty(null));
        $this->assertEquals(true, $this->validator->is_empty(''));
        $this->assertEquals(true, $this->validator->is_empty(array()));
        $this->assertEquals(true, $this->validator->is_empty(false));
        $this->assertEquals(true, $this->validator->is_empty(0));
        $this->assertEquals(true, $this->validator->is_empty('0'));
    }

    public function testInstantiates() {
        $this->assertInstanceOf('NeechyValidator', $this->validator);
    }
}
