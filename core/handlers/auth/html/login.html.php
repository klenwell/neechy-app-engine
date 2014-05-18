<?php
#
# Neechy Signup/Login Form
# Source: http://getbootstrap.com/examples/signin/
#

$t = $this;   # templater object
$t->append_to_head($t->css_link($t->css_href('login.css')));

# General vars
$alert = $t->data('alert');
$post_url = NeechyPath::url('login', 'auth');
$validation_errors = $t->data('validation-errors');

#
# Helper function
#
function auth_field($field, $attrs, $t) {
  $type = $attrs[0];
  $placeholder = $attrs[1];
  $autofocus = isset($attrs[2]) ? $attrs[2] : false;

  $value = ( $type == 'password' ) ? NULL : $t->data($field);
  $options = array(
    'class' => 'form-control',
    'placeholder' => $placeholder,
    'required' => NULL
  );
  if ( $autofocus ) {
    $options['autofocus'] = NULL;
  }
  $html = $t->input_field($type, $field, $value, $options);


  # Apply error styling if appropriate
  if ( isset($validation_errors[$field]) ) {
    $html = apply_field_state('error', $html);
  }

  return $html;
}

function apply_field_state($state, $inner_html) {
  $format = <<<HTML5
<div class="form-group %s">
  %s
</div>
HTML5;
  $state_class = sprintf('has-%s', $state);
  return sprintf($format, $state_class, $inner_html);
}

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
  $login_html[$field] = auth_field($field, $attrs, $t);
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
  $signup_html[$field] = auth_field($field, $attrs, $t);
}

?>
      <?php if (! empty($alert)): ?>
        <div class="alert"><?php echo $alert; ?></div>
      <?php endif; ?>

      <?php if (! empty($validation_errors)): ?>
      <div id="auth-errors" class="row">
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
          <?php echo $t->open_form($post_url, 'post', array('class' => 'form-login')); ?>
            <h2 class="form-signin-heading">Sign In</h2>
            <?php echo $login_html['login-name']; ?>
            <?php echo $login_html['login-pass']; ?>
            <label class="checkbox">
              <input type="checkbox" value="remember-me"> Remember me
            </label>
            <input name="action" type="hidden" value="login" />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
          <?php echo $t->close_form(); ?>
        </div>

        <div id="neechy-signup" class="well-sm col-xs-offset-2 col-xs-3">
          <?php echo $t->open_form($post_url, 'post', array('class' => 'form-signup')); ?>
            <h2 class="form-signin-heading">Sign Up</h2>
            <h4>And get your own wiki page!</h4>
            <?php echo $signup_html['signup-name']; ?>
            <?php echo $signup_html['signup-email']; ?>
            <?php echo $signup_html['signup-pass']; ?>
            <?php echo $signup_html['signup-pass-confirm']; ?>
            <?php echo $t->input_field('hidden', 'action', 'signup'); ?>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
          <?php echo $t->close_form(); ?>
        </div>
      </div>
