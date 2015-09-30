<?php

require_once('../core/handlers/page/php/helper.php');

$t = $this;   # templater object

$t->append_to_head($t->css_link('themes/bootstrap/css/editor.css'));

$page_helper = new PageHelper($t->request);


?>
      <!-- Tabs -->
      <div id="page-header">
        <ul id="page-tabs" class="nav nav-tabs">
          <?php echo $page_helper->build_page_tab_menu($t->data('page-title')); ?>
        </ul>
      </div>

      <!-- Tab Panes -->
      <div id="main-content">
        <div class="tab-content">
          <div class="tab-pane editor" id="editor">
            <div id="neechy-editor">
              <div id="wmd-editor" class="wmd-panel">
                <div id="wmd-button-bar"></div>
                <textarea class="form-control wmd-input" id="wmd-input"><?php echo $t->data('page-body') ?></textarea>
              </div>
              <div id="wmd-preview" class="wmd-panel wmd-preview well"></div>
            </div>

            <div class="actions">
              <button class="btn btn-primary preview">preview</button>
              <button class="btn btn-primary edit">edit</button>
              <button class="btn btn-info save">save</button>
                 <?php echo $t->open_form('', 'post', array('class' => 'save-page')); ?>
                  <textarea id="page-body" name="page-body" style="display:none;"></textarea>
                  <input type="hidden" name="action" value="save" />
                <?php echo $t->close_form(); ?>
            </div>
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
