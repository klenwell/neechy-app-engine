<?php

require_once('../core/handlers/page/php/helper.php');

$t = $this;   # templater object
$helper = new PageHelper($t->request);
$page = $t->data('page');

?>
      <!-- Tabs -->
      <div id="page-header">
        <?php echo $helper->build_page_tab_menu($page); ?>
      </div>

      <!-- Tab Panes -->
      <div id="main-content">
        <div class="timestamp alert alert-info">
          You are looking at the version of this page saved on <strong>
          <?php echo date('l jS F Y \a\t g:ia', strtotime($page->field('created_at'))); ?></strong>.
        </div>
        <div class="tab-content">
          <?php echo $page->body_to_html(); ?>
        </div>
      </div>

      <!-- Page Controls -->
      <div id="page-controls" class="navbar">
        <div class="container">
          <ul class="nav navbar-nav">
            <li><p class="navbar-text"><?php echo sprintf('Edited by %s on %s',
                                                          $page->editor_link(),
                                                          $page->field('created_at')); ?></p>
            </li>
          </ul>
        </div>
      </div>
