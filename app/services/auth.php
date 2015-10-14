<?php
/**
 * app/services/auth.php
 *
 * Neechy App Engine Auth Service
 *
 */
require_once('../core/services/base.php');
require_once('../core/neechy/errors.php');

use google\appengine\api\users\User;
use google\appengine\api\users\UserService;



class AppAuthService extends NeechyService {
    #
    # Properties
    #
    public $user = null;
    private $is_admin = false;

    #
    # Static API Methods
    #
    static public function redirect_user_if_not_admin($url=null) {
        $auth = new AppAuthService();

        if ( ! $auth->user ) {
            return $auth->redirect_to_login();
        }
        elseif ( ! $auth->user_is_admin() ) {
            return $auth->forbid_access();
        }
        else {
            return $auth;
        }
    }

    static public function user() {
        $auth = new AppAuthService();
        return $auth->user;
    }

    public function login_url() {
        return UserService::createLoginURL($_SERVER['REQUEST_URI']);
    }

    public function logout_url() {
        return UserService::createLogoutUrl('/');
    }


    #
    # Constructor
    #
    public function __construct() {
        $this->user = UserService::getCurrentUser();
        $this->is_admin = UserService::isCurrentUserAdmin();
    }

    #
    # Public Methods
    #
    public function user_is_admin() {
        return $this->is_admin;
    }

    public function redirect_to_login() {
        header('Location: ' . UserService::createLoginURL($_SERVER['REQUEST_URI']));
        exit();
    }

    #
    # Private Functions
    #
    protected function forbid_access() {
        throw new NeechyWebServiceError('You are not permitted to access this page.', 403);
    }

}
