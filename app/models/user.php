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
    public static function is_logged_in() {
        return AppAuthService::user_is_logged_in();
    }

    public static function create_on_install() {
        # Create System user
        $system_user_name = NEECHY_USER;
        $system_user_email = 'no-reply@neechy.github.com';
        $system_user = new User(array('name' => $system_user_name,
                                      'email' => $system_user_email,
                                      'status' => self::$STATUS_LEVELS['NEW']));
        $system_user->set_password(NeechySecurity::random_hex());
        $system_user->save();

        # Create Owner (user currently logged in)
        $app_engine_user = AppAuthService::user();

        if ( $app_engine_user ) {
            $owner_name = $app_engine_user->getNickname();
            $owner_email = $app_engine_user->getEmail();
            $owner = new User(array('name' => $owner_name,
                                    'email' => $owner_email,
                                    'status' => self::$STATUS_LEVELS['NEW']));
            $owner->set_password(NeechySecurity::random_hex());
            $owner->save();
        }
        else {
            $owner = null;
        }

        return array($system_user, $owner);
    }
}
