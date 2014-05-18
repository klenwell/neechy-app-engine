/*
 * editor.js
 *
 */
$(document).ready(function() {

  // Initialize PageDown editor
  var converter = Markdown.getSanitizingConverter();
  Markdown.Extra.init(converter, {table_class: "table table-striped"});
  var editor = new Markdown.Editor(converter);

  // PageDown hooks
  editor.hooks.chain("onPreviewRefresh", function () {
    //console.debug("the preview has been updated");
  });

  // Set page markdown / html
  var page_markdown = $('textarea#wmd-input').val();
  var page_html = converter.makeHtml(page_markdown);

  // Run editor
  editor.run();

  // Set up editor event listeners
  (function() {
    $('textarea#wmd-input').on('change keyup paste', function() {
      $('button.save').show();
      //console.debug('change');
    });

    $('button.save').click(function() {
      var user_edits = $('textarea#wmd-input').val();
      $('textarea#page-body').val(user_edits);
      $('form.save-page').submit();
    });
  })();

  // Set up editor UI
  (function() {
    $('div.tab-pane#read').html(page_html);

    $('#wmd-preview').hide();
    $('button.edit').hide();
    $('button.save').hide();

    $('button.preview').click(function() {
      editor.refreshPreview();
      $('#wmd-editor').hide();
      $('#wmd-preview').show();
      $('button.preview').hide();
      $('button.edit').show();
    });

    $('button.edit').click(function() {
      $('#wmd-preview').hide();
      $('#wmd-editor').show();
      $('button.edit').hide();
      $('button.preview').show();
    });
  })();

});
