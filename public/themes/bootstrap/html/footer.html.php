<?php

$t = $this;   # templater object

$auth = new AppAuthService();

?>
    <?php if ( NeechyConfig::stage() == 'dev' ): ?>
      <div class="dev-footer">
        <h4>App Engine Dev Server</h4>
        <p>Using <?php echo NeechyConfig::environment(); ?> config settings.</p>
        <?php if ( $auth->user ): ?>
        <p>Logged in as <?php echo ( $auth->user_is_admin() ) ? 'admin' : 'user' ?>.</p>
        <?php else: ?>
        <p>Not logged in.</p>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="dev-footer">
        Please note: Database data will be reset periodically.
      </div>
    <?php endif; ?>

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
