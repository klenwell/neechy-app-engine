<?php
/**
 * index.php
 *
 * This is the main Neechy script. This file is called each time a request is
 * made from the browser.
 *
 */
require_once('../core/libs/config.php');
require_once('../core/libs/templater.php');


$content = <<<HTML5
  <p>For more information, visit
    <a href="https://github.com/klenwell/neechy">the Neechy Github site</a>.
  </p>
HTML5;

# Init config here for usage elsewhere
NeechyConfig::init();

$templater = new NeechyTemplater();
$templater->set('content', $content);
print $templater->render();
