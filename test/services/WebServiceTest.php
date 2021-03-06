<?php
/**
 * test/services/WebServiceTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php services/WebServiceTest
 *
 */
require_once('../core/services/web.php');
require_once('../core/neechy/config.php');
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
    public function testShouldRenderPage() {
        # Cannot test using PhpUnit. Unable to mock NeechyRequest call within NeechyWebService
        # serve method call without refactoring code.
    }

    public function testShouldReturn404StatusForMissingHandler() {
        # Cannot test using PhpUnit. See note above.
    }

    public function testInstantiates() {
        $config = NeechyConfig::init();
        $web_service = new NeechyWebService($config);
        $this->assertInstanceOf('NeechyWebService', $web_service);
    }
}
