<?php
#
# Neechy Signup/Login Form
# Source: http://getbootstrap.com/examples/signin/
#


$t = $this;   # templater object

$alert = $t->data('alert');
$post_url = NeechyPath::url('login', 'auth');

?>
      <?php if (! empty($alert)): ?>
        <div class="alert"><?php echo $alert ?></div>
      <?php endif; ?>

      <div id="neechy-auth" class="row">
        <div id="neechy-login" class="col-xs-offset-2 col-xs-3">
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
            <label class="checkbox">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
          </form>
        </div>

        <div id="neechy-signup" class="col-xs-offset-2 col-xs-4">
          <form class="form-signup" role="form" method="post"
                action="<?php echo $post_url; ?>">
            <h2 class="form-signin-heading">Sign Up</h2>
            <p>And get your own wiki page!</p>
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
