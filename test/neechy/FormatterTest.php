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
            array('[TitleLink](/page/TitleLink)',
                  '<p><a href="/page/TitleLink">TitleLink</a></p>'),
            array('[[TitleLink Title Link]]',
                  '<p><a href="/page/TitleLink">Title Link</a></p>'),
            array('x [[TitleLink Title Link]] x',
                  '<p>x <a href="/page/TitleLink">Title Link</a> x</p>'),
            array('[[TitleLink | Title Link]]',
                  '<p><a href="/page/TitleLink">Title Link</a></p>'),
            array('[[TitleLink]]',
                  '<p><a href="/page/TitleLink">TitleLink</a></p>'),
            array('[[http://github.com/ Github]]',
                  '<p><a href="http://github.com/">Github</a></p>'),
            array('x [[http://github.com/ Github]] x',
                  '<p>x <a href="http://github.com/">Github</a> x</p>'),
            array('[[http://github.com/ | Github]]',
                  '<p><a href="http://github.com/">Github</a></p>'),
            array('http://github.com/',
                  '<p><a href="http://github.com/">http://github.com/</a></p>'),

            # Leave links in code blocks alone
            array('    NeechyFormatting',
                  '<pre><code>NeechyFormatting</code></pre>'),

            # TitleCase (not currently supported)
            #array('TitleCase',
            #      '<p><a href="/page/TitleCase">TitleCase</a></p>'),
            #array('word TitleCase word',
            #      '<p>word <a href="/page/TitleCase">TitleCase</a> word</p>')
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
