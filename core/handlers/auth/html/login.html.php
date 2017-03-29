<?php
#
# Neechy Signup/Login Form
# Source: http://getbootstrap.com/examples/signin/
#
require_once('../core/neechy/path.php');
require_once('../public/themes/bootstrap/php/helper.php');
require_once('../core/handlers/auth/php/helper.php');

$t = $this;   # templater object
$helper = new BootstrapHelper($t->request);
$auth_helper = new AuthHelper($t->request);

$t->append_to_head($t->css_link($t->css_href('form.css')));

# General vars
$alert = $t->data('alert');
$post_url = NeechyPath::url('auth', 'login');
$validation_errors = $t->data('validation-errors');

#
# Login Form Setup
#
$login_fields = array(
  'login-name' => array('text', 'UserName', true),
  'login-pass' => array('password', 'Password'),
);

# Generate html
$login_html = array();
foreach ( $login_fields as $field => $attrs ) {
  $login_html[$field] = $auth_helper->auth_field($field, $attrs, $t, $helper);
}

#
# Signup Form Setup
#
# Fields
$signup_fields = array(
  'signup-name' => array('text', sprintf('UserName (%d chars min)',
      SignUpValidator::MIN_USERNAME_LENGTH)),
  'signup-email' => array('email', 'Email Address'),
  'signup-pass' => array('password', sprintf('Password (%d chars min)',
      SignUpValidator::MIN_PASSWORD_LENGTH)),
  'signup-pass-confirm' => array('password', 'Password (confirm)')
);

# Generate html
$signup_html = array();
foreach ( $signup_fields as $field => $attrs ) {
  $signup_html[$field] = $auth_helper->auth_field($field, $attrs, $t, $helper);
}

?>
    <div class="auth handler login">
      <?php if (! empty($alert)): ?>
        <div class="alert"><?php echo $alert; ?></div>
      <?php endif; ?>

      <?php if (! empty($validation_errors)): ?>
      <div class="row form errors">
        <div class="panel panel-danger">
          <div class="panel-body">
            <h4>There was a problem:</h4>
            <ul class="errors">
            <?php foreach ($validation_errors as $field => $messages): ?>
              <?php foreach ($messages as $message): ?>
              <li class="error"><?php echo $message; ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div id="neechy-auth" class="row">
        <div id="neechy-login" class="well-sm col-xs-offset-2 col-xs-3">
          <?php echo $helper->open_form($post_url, 'post', array('class' => 'form-login')); ?>
            <h3>Sign In</h3>
            <?php echo $login_html['login-name']; ?>
            <?php echo $login_html['login-pass']; ?>
            <label class="checkbox">
              <input type="checkbox" value="remember-me"> Remember me
            </label>
            <input name="purpose" type="hidden" value="login" />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
          <?php echo $helper->close_form(); ?>
        </div>

        <div id="neechy-signup" class="well-sm col-xs-offset-2 col-xs-3">
          <?php echo $helper->open_form($post_url, 'post', array('class' => 'form-signup')); ?>
            <h3>Sign Up</h3>
            <h4>And get your own wiki page!</h4>
            <?php echo $signup_html['signup-name']; ?>
            <?php echo $signup_html['signup-email']; ?>
            <?php echo $signup_html['signup-pass']; ?>
            <?php echo $signup_html['signup-pass-confirm']; ?>
            <input name="purpose" type="hidden" value="signup" />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
          <?php echo $helper->close_form(); ?>
        </div>
      </div>
    </div>
