<?php

require_once('../core/handlers/page/php/helper.php');

$t = $this;   # templater object
$page_helper = new PageHelper($t->request);

$t->append_to_head($t->css_link('/themes/bootstrap/css/editor.css'));

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
              <?php echo $page_helper->open_form('', 'post', array('class' => 'save-page')); ?>

                <?php # Preview Panel ?>
                <?php if ( $t->data('purpose') == 'preview' ): ?>
                <div id="wmd-preview" class="wmd-panel wmd-preview well">
                  <?php echo $t->data('preview') ?>
                </div>
                <textarea class="form-control wmd-input hidden"
                          name="wmd-input"
                          id="wmd-input"><?php echo $t->data('page-body') ?></textarea>

                <?php # Editor ?>
                <?php else: ?>
                <div id="wmd-editor" class="wmd-panel">
                  <div id="wmd-button-bar"></div>
                  <textarea class="form-control wmd-input"
                            name="wmd-input"
                            id="wmd-input"><?php echo $t->data('page-body') ?></textarea>
                </div>
                <?php endif; ?>

                <div class="actions">
                  <?php if ( $t->data('purpose') == 'preview' ): ?>
                  <input type="submit" class="btn btn-primary edit" name="purpose" value="edit" />
                  <?php else: ?>
                  <input type="submit" class="btn btn-primary preview" name="purpose" value="preview" />
                  <?php endif; ?>
                  <input type="submit" class="btn btn-info save" name="purpose" value="save" />
                </div>
              <?php echo $page_helper->close_form(); ?>
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
