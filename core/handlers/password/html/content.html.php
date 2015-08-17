<?php
#
# Neechy Password View
#
require_once('../core/handlers/password/php/helper.php');

$t = $this;   # templater object
$t->append_to_head($t->css_link($t->css_href('form.css')));
$validator = $t->data('form-validator');
$helper = new PasswordHelper();

# General vars
$post_url = NeechyPath::url('change', 'password');


?>
    <div class="password handler">
      <h2>Password</h2>

      <div id="neechy-pass" class="row">
        <div id="neechy-login" class="well-sm col-xs-offset-1 col-xs-5">
          <?php echo $helper->open_form($post_url); ?>
            <h3>Change Password</h2>
            <?php echo $helper->password_group('old-password', 'Old Password',
                                               true, $validator); ?>
            <?php echo $helper->password_group('new-password', 'New Password (8 chars min)',
                                               false, $validator); ?>
            <?php echo $helper->password_group('new-password-confirm', 'New Password (confirm)',
                                               false, $validator); ?>
            <?php echo $helper->submit_button('Submit'); ?>
          <?php echo $helper->close_form('change-password'); ?>
        </div>
        <div id="neechy-login" class="well-sm col-xs-6"></div>
      </div>
    </div>
