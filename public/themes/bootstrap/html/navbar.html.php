<?php
require_once('../public/themes/bootstrap/php/helper.php');


$t = $this;   # templater object
$helper = new BootstrapHelper($t->request);

?>
    <!-- Bootstrap Navbar -->
    <div role="navigation" class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button data-target=".navbar-collapse" data-toggle="collapse"
                  class="navbar-toggle" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="/" class="navbar-brand">Neechy</a>
        </div>

        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="<?php echo $helper->nav_tab_class('PageIndex'); ?>">
              <?php echo $helper->neechy_link('PageIndex'); ?>
            </li>
            <!-- TODO: dynamically build menu
            <li class="<?php #echo $helper->nav_tab_class('Niches'); ?>">
              <?php #echo $helper->neechy_link('Niches'); ?>
            </li>
            <li class="<?php #echo $helper->nav_tab_class('RecentChanges'); ?>">
              <?php #echo $helper->neechy_link('RecentChanges'); ?>
            </li>
            <li class="<?php #echo $helper->nav_tab_class('RecentComments'); ?>">
              <?php #echo $helper->neechy_link('RecentComments'); ?>
            </li>
            -->
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li>
              <?php echo $helper->user_button(); ?>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
