<?php

$t = $this;   # templater object
$t->append_to_head($t->css_link('themes/bootstrap/css/editor.css'));
$t->append_to_body($t->js_link('themes/bootstrap/js/open/pagedown/Markdown.Converter.js'));
$t->append_to_body($t->js_link('themes/bootstrap/js/open/pagedown/Markdown.Sanitizer.js'));
$t->append_to_body($t->js_link('themes/bootstrap/js/open/pagedown/Markdown.Editor.js'));
$t->append_to_body($t->js_link('themes/bootstrap/js/open/pagedown/Markdown.Extra.js'));
$t->append_to_body($t->js_link('themes/bootstrap/js/editor.js'));

$input = $t->data('editor-content');

?>

      <div id="neechy-editor">
        <div id="wmd-editor" class="wmd-panel">
          <div id="wmd-button-bar"></div>
          <textarea class="form-control wmd-input" id="wmd-input"><?php echo $input; ?></textarea>
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
