<?php
/**
 * test/neechy/SecurityTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/SecurityTest
 *
 */
require_once('../core/neechy/security.php');
require_once('../test/helper.php');


class NeechySecurityTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        # Suppresses error in Travis-CI
        @NeechySecurity::start_session();
    }

    public function tearDown() {
        NeechyTestHelper::destroy_session();
    }

    /**
     * Tests
     */
    public function testShouldNotRaiseCSRFError() {
        # Create CSRF token (no post yet so doesn't authenticate)
        NeechySecurity::prevent_csrf();

        # Test successful authentication
        $_POST['csrf_token'] = $_SESSION['csrf_token'];
        $this->assertTrue(NeechySecurity::prevent_csrf());
    }

    public function testThatInvalidCSRFTokenFromPostRaisesError() {
        # Create CSRF token (no post yet so doesn't authenticate)
        NeechySecurity::prevent_csrf();

        # Test failed authentication
        $this->setExpectedException('NeechyCsrfError');
        $_POST['csrf_token'] = 'invalid-token';
        NeechySecurity::prevent_csrf();
    }

    public function testThatExpiredCSRFSessionRaisesError() {
        # Create CSRF token (no post yet so doesn't authenticate)
        NeechySecurity::prevent_csrf();

        # Simulate POST form rendered with valid CSRF token
        $_POST['csrf_token'] = $_SESSION['csrf_token'];

        # Simulate SESSION expiring before form posted
        session_unset();

        # Simulate form post
        $this->setExpectedException('NeechyCsrfError');
        NeechySecurity::prevent_csrf();
    }

    public function testHashPassword() {
        $password = 'neechy123';
        $stored_hash = NeechySecurity::hash_password($password);

        $verified = NeechySecurity::verify_password($password, $stored_hash);
        $this->assertTrue($verified);

        $unverified = NeechySecurity::verify_password("don't remember", $stored_hash);
        $this->assertFalse($unverified);
    }
}
