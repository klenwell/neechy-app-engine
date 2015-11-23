<?php
/**
 * test/neechy/FormatterTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/FormatterTest
 *
 */
require_once('../core/neechy/formatter.php');


class FormatterTest extends PHPUnit_Framework_TestCase {

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
    public function testWikiLinkTranslation() {
        $test_cases = array(
            # [$wml, $expected]
            array('[CamelLink](/page/CamelLink)',
                  '<p><a href="/page/CamelLink">CamelLink</a></p>'),
            array('[[CamelLink Camel Link]]',
                  '<p><a href="/page/CamelLink">Camel Link</a></p>'),
            array('x [[CamelLink Camel Link]] x',
                  '<p>x <a href="/page/CamelLink">Camel Link</a> x</p>'),
            array('[[CamelLink | Camel Link]]',
                  '<p><a href="/page/CamelLink">Camel Link</a></p>'),
            array('[[http://github.com/ Github]]',
                  '<p><a href="http://github.com/">Github</a></p>'),
            array('x [[http://github.com/ Github]] x',
                  '<p>x <a href="http://github.com/">Github</a> x</p>'),
            array('[[http://github.com/ | Github]]',
                  '<p><a href="http://github.com/">Github</a></p>'),

            # Leave links in code blocks alone
            array('    NeechyFormatting',
                  '<pre><code>NeechyFormatting</code></pre>'),

            # TitleCase
            array('CamelLink',
                  '<p><a href="/page/CamelLink">CamelLink</a></p>'),
            array('word CamelLink word',
                  '<p>word <a href="/page/CamelLink">CamelLink</a> word</p>')
        );

        $formatter = new NeechyFormatter();

        foreach ( $test_cases as $case ) {
            list($wml, $expected) = $case;
            $html = $formatter->wml_to_html($wml);
            $this->assertEquals($expected, $html, sprintf('Failed for case: %s', $wml));
        }
    }

    public function testInstantiates() {
        $formatter = new NeechyFormatter();
        $this->assertInstanceOf('NeechyFormatter', $formatter);
    }
}
