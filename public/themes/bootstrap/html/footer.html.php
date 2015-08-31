<?php

$t = $this;   # templater object

?>

    <footer>
      <div class="container">
        <p>
          Template theme built with <?php echo
            $t->link('http://twitter.github.io/bootstrap/', 'Bootstrap'); ?>
        </p>
        <p>
          Powered by <?php echo
            $t->link('https://github.com/klenwell/neechy', 'Neechy'); ?>
        </p>
      </div>
    </footer>
    <?php if ( NeechyConfig::environment() == 'test' ) { ?>
      <div class="dev-footer">
        <h4>Dev Environment</h4>
        <p>Using test config settings.</p>
        <p>To sign in: NeechyAdmin / neechy123</p>
      </div>
    <?php } ?>
