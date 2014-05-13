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

# Helper function
function apply_field_state($state, $inner_html) {
  $format = <<<HTML5
<div class="form-group %s">
  %s
</div>
HTML5;
  $state_class = sprintf('has-%s', $state);
  return sprintf($format, $state_class, $inner_html);
}

# Generate html
$signup_html = array();
foreach ( $signup_fields as $field => $attrs ) {
  list($type, $placeholder) = $attrs;
  $value = ( $type == 'password' ) ? NULL : $t->data($field);
  $options = array(
    'class' => 'form-control',
    'placeholder' => $placeholder,
    'required' => NULL
  );
  $html = $t->input_field($type, $field, $value, $options);

  # Apply error styling if appropriate
  if ( isset($validation_errors[$field]) ) {
    $html = apply_field_state('error', $html);
  }

  $signup_html[$field] = $html;
}

?>
      <?php if (! empty($alert)): ?>
        <div class="alert"><?php echo $alert; ?></div>
      <?php endif; ?>

      <div id="neechy-auth" class="row">
        <?php if (! empty($validation_errors)): ?>
        <div class="errors">
          <h4>Please correct the following errors and resubmit:</h4>
          <ul class="errors">
            <?php foreach ($validation_errors as $field => $messages): ?>
              <?php foreach ($messages as $message): ?>
              <li class="error"><?php echo $message; ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <div id="neechy-login" class="well-sm col-xs-offset-2 col-xs-3">
          <form class="form-login" role="form" method="post"
                action="<?php echo $post_url; ?>">
            <h2 class="form-signin-heading">Sign In</h2>
            <input name="login-name" type="text" placeholder="UserName"
                   class="form-control" required autofocus>
            <input name="login-pass" type="password" placeholder="Password"
                   class="form-control" required>
            <label class="checkbox">
              <input type="checkbox" value="remember-me"> Remember me
            </label>
            <input name="action" type="hidden" value="login" />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
          </form>
        </div>

        <div id="neechy-signup" class="well-sm col-xs-offset-2 col-xs-3">
          <?php echo $t->open_form($post_url, 'post', array('class' => 'form-signup')); ?>
            <h2 class="form-signin-heading">Sign Up</h2>
            <h4>And get your own wiki page!</h4>
            <?php echo $signup_html['signup-name']; ?>
            <?php echo $signup_html['signup-email']; ?>
            <?php echo $signup_html['signup-pass']; ?>
            <?php echo $signup_html['signup-pass-confirm']; ?>
            <input name="action" type="hidden" value="register" />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
          </form>
        </div>
      </div>
