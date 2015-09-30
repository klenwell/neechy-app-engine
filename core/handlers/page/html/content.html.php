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
          <div class="tab-pane page active" id="read">
            <?php echo $t->data('page-body'); ?>
          </div>
          <div class="tab-pane edit" id="edit">
          </div>
          <div class="tab-pane history" id="history">
          </div>
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
