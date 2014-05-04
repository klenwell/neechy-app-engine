<?php

$t = $this;   # templater object
$t->append_to_head($t->css_link('themes/bootstrap/css/editor.css'));
$t->append_to_body($t->js_link('themes/bootstrap/js/open/pagedown/Markdown.Converter.js'));
$t->append_to_body($t->js_link('themes/bootstrap/js/open/pagedown/Markdown.Sanitizer.js'));
$t->append_to_body($t->js_link('themes/bootstrap/js/open/pagedown/Markdown.Editor.js'));
$t->append_to_body($t->js_link('themes/bootstrap/js/open/pagedown/Markdown.Extra.js'));
$t->append_to_body($t->js_link('themes/bootstrap/js/editor.js'));

?>

      <div id="neechy-editor">
        <div id="wmd-editor" class="wmd-panel">
          <div id="wmd-button-bar"></div>
          <textarea class="form-control wmd-input" id="wmd-input"></textarea>
        </div>
        <div id="wmd-preview" class="wmd-panel wmd-preview well"></div>
      </div>

      <div class="actions">
        <button class="btn btn-default preview">preview</button>
        <button class="btn btn-default edit">edit</button>
        <button class="btn btn-info save">save</button>
          <form class="save-page" method="post">
            <textarea id="page-body" name="page-body" style="display:none;"></textarea>
            <input type="hidden" name="page-action" value="save" />
          </form>
      </div>
