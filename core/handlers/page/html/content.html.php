<?php

require_once('../core/handlers/page/php/helper.php');

$t = $this;   # templater object
$helper = new PageHelper($t->request);

?>
      <!-- Tabs -->
      <div id="page-header">
        <ul id="page-tabs" class="nav nav-tabs">
          <?php echo $helper->build_page_tab_menu($t->data('page-title')); ?>
        </ul>
      </div>

      <!-- Tab Panes -->
      <div id="main-content">
        <div class="tab-content">
          <?php echo $helper->build_tab_panels($t->data('panel-content')); ?>
        </div>
      </div>

      <!-- Page Controls -->
      <div id="page-controls" class="navbar">
        <div class="container">
          <ul class="nav navbar-nav">
            <li><p class="navbar-text"><?php echo $t->data('last-edited'); ?></p></li>
          </ul>
        </div>
      </div>
