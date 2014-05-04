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
    console.debug("the preview has been updated");
  });

  // Run editor
  editor.run();

  // Set up editor event listeners
  (function() {
    $('textarea#wmd-input').on('change keyup paste', function() {
      $('button.save').show();
      console.debug('change');
    });

    $('button.save').click(function() {
      console.debug('saving page');
      console.debug($('textarea#wmd-input').val());
      $('textarea#page-body').val($('textarea#wmd-input').val());
      $('form.save-page').submit();
    });
  })();

  // Set up editor UI
  (function() {
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
