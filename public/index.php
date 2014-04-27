<?php

require_once('../core/libs/templater.php');


$content = <<<HTML5
  <p>For more information, visit
    <a href="https://github.com/klenwell/neechy">the Neechy Github site</a>.
  </p>
HTML5;

$templater = new NeechyTemplater();
$templater->set('content', $content);
print $templater->render();
