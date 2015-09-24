<?php

$t = $this;   # templater object

#
# Helper Functions
#
function build_page_tab_menu($t) {
  $page_tabs = array(
    # key => array(label, link, panel content)
    'edit' => array('Edit', '#edit', $t->data('editor')),
    'discuss' => array('Discuss', '#discuss', 'Under development: a comment space'),
    'history' => array('History', '/?handler=history&page=%s', 'Under development: list of page edits'),
    'access' => array('Access', '#access', 'Under development: page access control form')
  );
  $user_tabs = array(
    'default' => array('edit>hide', 'discuss', 'history'),
    'logged-in' => array('edit', 'discuss', 'history', 'access')
  );

  $tab_format = '<li><a href="%s"%s>%s</a></li>';
  $panel_format = '<div class="tab-pane" id="%s">%s</div>';

  $tabs = array(
    sprintf('<li class="title active"><a href="#read" data-toggle="tab">%s</a></li>',
      $t->data('page-title'))
  );
  $panels = array(
    '<div class="tab-pane active" id="read"></div>'
  );

  $key = ( User::is_logged_in() ) ? 'logged-in' : 'default';
  $tab_list = $user_tabs[$key];

  foreach ( $tab_list as $tab_key ) {
    if ( strpos($tab_key, '>') !== FALSE ) {
      list($tab_key, $mod) = explode('>', $tab_key);
    }
    else {
      $mod = 'show';
    }

    list($tab_label, $tab_link_f, $tab_content) = $page_tabs[$tab_key];

    if ( substr_count($tab_link_f, '%s') > 0 ) {
      $tab_link = sprintf($tab_link_f, $t->request->page);
      $data_toggle = '';
    }
    else {
      $tab_link = $tab_link_f;
      $data_toggle = ' data-toggle="tab"';
    }

    if ( $mod !== 'hide' ) {
      $tabs[] = sprintf($tab_format, $tab_link, $data_toggle, $tab_label);
    }
    $panels[] = sprintf($panel_format, $tab_key, $tab_content);
  }

  return array(
    'tab-list' => implode("\n", $tabs),
    'panel-list' => implode("\n", $panels)
  );
}

$view_html = build_page_tab_menu($t);

?>
      <!-- Tabs -->
      <div id="page-header">
        <ul id="page-tabs" class="nav nav-tabs">
          <?php echo $view_html['tab-list']; ?>
        </ul>
      </div>

      <!-- Tab Panes -->
      <div id="main-content">
        <div class="tab-content">
          <?php echo $view_html['panel-list']; ?>
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
