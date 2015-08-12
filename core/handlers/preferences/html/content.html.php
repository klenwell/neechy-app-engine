<?php
#
# Neechy Preferences View
#

$t = $this;   # templater object
#$t->append_to_head($t->css_link($t->css_href('login.css')));

# General vars
$post_url = NeechyPath::url('change', 'preferences');


#
# Helper function
#
function password_group($name, $placeholder, $autofocus, $t) {
  $attrs = [
    'class' => 'form-control',
    'placeholder' => $placeholder
  ];

  if ( $autofocus ) {
    $attrs['autofocus'] = NULL;
  }

  return $t->bootstrap_form_group(
    $t->password_field($name, null, $attrs)
  );
}


?>
    <div class="preferences">
      <h2>Preferences</h2>

      <div id="neechy-pass" class="row well">
        <?php echo $t->open_form($post_url, 'post', array('class' => 'form-login')); ?>
          <h3 class="form-signin-heading">Change Password</h2>
          <?php echo password_group('old-password', 'Old Password', true, $t); ?>
          <?php echo password_group('new-password', 'New Password (8 chars min)', false, $t); ?>
          <?php echo password_group('new-password-confirm', 'New Password (confirm)', false, $t); ?>
          <?php echo $t->submit_button('Submit',
              array('class' => 'btn btn-lg btn-primary btn-block')); ?>
        <?php echo $t->close_form('change-password'); ?>
      </div>
    </div>
