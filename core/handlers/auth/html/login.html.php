<?php
#
# Neechy Signup/Login Form
# Source: http://getbootstrap.com/examples/signin/
#

$t = $this;   # templater object
$t->append_to_head($t->css_link($t->css_href('login.css')));

$alert = $t->data('alert');
$validation_errors = $t->data('validation-errors');

$post_url = NeechyPath::url('login', 'auth');

?>
      <?php if (! empty($alert)): ?>
        <div class="alert"><?php echo $alert ?></div>
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
          <form class="form-signup" role="form" method="post"
                action="<?php echo $post_url; ?>">
            <h2 class="form-signin-heading">Sign Up</h2>
            <h4>And get your own wiki page!</h4>
            <input name="signup-name" type="text"
              placeholder="UserName" class="form-control" required>
            <input name="signup-email" type="email" placeholder="Email address"
                   class="form-control" required>
            <input name="signup-pass" type="password"
              placeholder="Password" class="form-control" required>
            <input name="signup-pass-confirm" type="password"
              placeholder="Password (confirm)" class="form-control" required>
            <input name="action" type="hidden" value="register" />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
          </form>
        </div>
      </div>
