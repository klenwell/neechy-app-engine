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
            <li><?php echo $t->neechy_link('Niches'); ?></li>
            <li><?php echo $t->neechy_link('PageIndex'); ?></li>
            <li><?php echo $t->neechy_link('RecentChanges'); ?></li>
            <li><?php echo $t->neechy_link('RecentComments'); ?></li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li>
                <div class="a-requires-parent">
                  <a href="#" class="btn btn-primary navbar-btn">Login / SignUp</a>
                </div>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
