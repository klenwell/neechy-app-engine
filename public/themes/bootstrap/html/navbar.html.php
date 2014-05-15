<?php

$t = $this;   # templater object

?>

    <div role="navigation" class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="/" class="navbar-brand">Neechy</a>
        </div>

        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <!-- TODO: dynamically build menu -->
            <li class="<?php echo $t->nav_tab_class('Niches'); ?>">
              <?php echo $t->neechy_link('Niches'); ?>
            </li>
            <li class="<?php echo $t->nav_tab_class('PageIndex'); ?>">
              <?php echo $t->neechy_link('PageIndex'); ?>
            </li>
            <li class="<?php echo $t->nav_tab_class('RecentChanges'); ?>">
              <?php echo $t->neechy_link('RecentChanges'); ?>
            </li>
            <li class="<?php echo $t->nav_tab_class('RecentComments'); ?>">
              <?php echo $t->neechy_link('RecentComments'); ?>
            </li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li>
                <div class="a-requires-parent">
                  <?php echo $t->neechy_link('Login / SignUp', 'login', 'auth', NULL,
                      array('class' => 'btn btn-primary navbar-btn')); ?>
                </div>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
