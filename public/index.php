<?php

require_once('../core/libs/templater.php');


$templater = new NeechyTemplater();

$templater->partial['head'] = <<<HTML5
<head>
  <title>Neechy</title>
</head>
HTML5;

$templater->partial['top'] = '<h1>Neechy</h1>';

$templater->partial['middle'] = <<<HTML5
  <p>For more information, visit
    <a href="https://github.com/klenwell/neechy">the Neechy Github site</a>.
  </p>
HTML5;

$templater->partial['bottom'] = <<<HTML5
  <footer>
    some rights reserved
  </footer>
HTML5;

print $templater->render();
