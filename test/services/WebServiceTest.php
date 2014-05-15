<?php
/**
 * test/services/WebServiceTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php services/WebServiceTest
 *
 */
require_once('../core/services/web.php');
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');


class WebServiceTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
        PageFixture::init();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
    }

    /**
     * Tests
     */
    public function testInstantiates() {
        $web_service = new NeechyWebService(NeechyTestHelper::TEST_CONF_PATH);
        $this->assertInstanceOf('NeechyWebService', $web_service);
    }
}
