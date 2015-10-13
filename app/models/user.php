<?php
/**
 * app/models/user.php
 *
 * Neechy User model class.
 *
 */
require_once('../core/models/user.php');
require_once('../app/services/auth.php');


class AppUser extends User {

    /*
     * Static Methods
     */
    public static function create_on_install() {
        # Create System user
        $name = NEECHY_USER;
        $email = 'no-reply@neechy.github.com';
        $password = NeechySecurity::random_hex();
        $system_user = User::register($name, $email, $password);

        # Create Owner (user currently logged in)
        $app_engine_user = AppAuthService::user();
        $owner_name = $app_engine_user->getNickname();
        $owner_email = $app_engine_user->getEmail();
        $owner = User::register($owner_name, $owner_email, NeechySecurity::random_hex());
    }
}
