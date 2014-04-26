<?php
    $partials = array();
    $partials['head'] = <<<HTML5
<head>
  <title>Neechy</title>
</head>
HTML5;

    $partials['top'] = '<h1>Neechy</h1>';

    $partials['middle'] = <<<HTML5
  <p>For more information, visit
    <a href="https://github.com/klenwell/neechy">the Neechy Github site</a>.
  </p>
HTML5;

    $partials['bottom'] = <<<HTML5
  <footer>
    some rights reserved
  </footer>
HTML5;


    $layout_file = '../core/templates/layout.html.php';
    $output = file_get_contents($layout_file);

    foreach ($partials as $partial => $content) {
        $output = str_replace(sprintf('{{ %s }}', $partial), $content, $output);
    }

    print $output;
