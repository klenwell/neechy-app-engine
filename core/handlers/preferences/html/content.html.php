<?php
#
# Neechy Preferences View
#
require_once('../core/handlers/preferences/php/helper.php');

$t = $this;   # templater object
$helper = new PreferencesHelper();

# General vars
$post_url = NeechyPath::url('change', 'preferences');


?>
    <div class="preferences">
      <h2>Preferences</h2>

      <div id="neechy-pass" class="row">
        <div id="neechy-login" class="well-sm col-xs-offset-1 col-xs-5">
          <?php echo $helper->open_bootstrap_form($post_url); ?>
            <h3 class="form-signin-heading">Change Password</h2>
            <?php echo $helper->password_group('old-password', 'Old Password', true, $t); ?>
            <?php echo $helper->password_group('new-password', 'New Password (8 chars min)',
                                      false, $t); ?>
            <?php echo $helper->password_group('new-password-confirm', 'New Password (confirm)',
                                      false, $t); ?>
            <?php echo $helper->bootstrap_submit_button('Submit'); ?>
          <?php echo $helper->close_form('change-password'); ?>
        </div>
        <div id="neechy-login" class="well-sm col-xs-6"></div>
      </div>
    </div>
