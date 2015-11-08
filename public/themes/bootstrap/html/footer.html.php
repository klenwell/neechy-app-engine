<?php
require_once('../public/themes/bootstrap/php/helper.php');

$t = $this;   # templater object
$helper = new BootstrapHelper($t->request);

?>
    <?php if ( NeechyConfig::environment() == 'test' ) { ?>
      <div class="dev-footer">
        <h4>Dev Environment</h4>
        <p>Using test config settings.</p>
        <p>To sign in: NeechyAdmin / neechy123</p>
      </div>
    <?php } ?>

    <footer>
      <div class="container">
        <p>
          Template theme built with <?php echo
            $helper->link('http://twitter.github.io/bootstrap/', 'Bootstrap'); ?>
        </p>
        <p>
          Powered by <?php echo
            $helper->link('https://github.com/klenwell/neechy', 'Neechy'); ?>
        </p>
      </div>
    </footer>
