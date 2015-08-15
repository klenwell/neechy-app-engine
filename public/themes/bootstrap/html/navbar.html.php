<?php

$t = $this;   # templater object

if ( User::is_logged_in() ) {
  $logged_in_dropdown = <<<HTML5
    <div class="btn btn-group">
      <button type="button" class="btn btn-info">%s</button>
      <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
      </button>
      <ul class="dropdown-menu">
        <li>%s</li>
        <li>%s</li>
      </ul>
    </div>
HTML5;

  $user_name = User::logged_in('name');
  $right_button = sprintf($logged_in_dropdown,
                          $user_name,
                          $t->neechy_link('Preferences', $user_name, 'preferences'),
                          $t->neechy_link('Logout', 'logout', 'auth'));
}
else {
  $format = <<<HTML5
    <div class="a-requires-parent">
      %s
    </div>
HTML5;

  $link = $t->neechy_link('Login / SignUp', 'login', 'auth', NULL,
                          array('class' => 'btn btn-primary navbar-btn'));
  $right_button = sprintf($format, $link);
}

?>

    <div role="navigation" class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="/" class="navbar-brand">Neechy</a>
        </div>

        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <!-- TODO: dynamically build menu -->
            <li class="<?php echo $t->nav_tab_class('Niches'); ?>">
              <?php echo $t->neechy_link('Niches'); ?>
            </li>
            <li class="<?php echo $t->nav_tab_class('PageIndex'); ?>">
              <?php echo $t->neechy_link('PageIndex'); ?>
            </li>
            <li class="<?php echo $t->nav_tab_class('RecentChanges'); ?>">
              <?php echo $t->neechy_link('RecentChanges'); ?>
            </li>
            <li class="<?php echo $t->nav_tab_class('RecentComments'); ?>">
              <?php echo $t->neechy_link('RecentComments'); ?>
            </li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li>
              <?php echo $right_button; ?>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
