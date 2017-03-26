<?php
/**
 * app/services/app_engine.php
 *
 * Neechy App Engine Service class.
 *
 */
require_once('../core/services/web.php');



class NeechyAppEngineService extends NeechyWebService {
    #
    # Properties
    #

    #
    # Constructor
    #

    #
    # Public Methods
    #
    public function validate_environment() {
        if ( NeechyConfig::stage() == 'cloud' && NeechyConfig::environment() == 'app' ) {
            return true;
        }
        elseif ( NeechyConfig::stage() == 'dev' || NeechyConfig::environment() == 'test' ) {
            $this->setup_dev_environment();
            return true;
        }
        else {
            $format = 'Config file missing. Please see %s for install help.';
            $link = '<a href="https://github.com/klenwell/neechy">Neechy README file</a>';
            throw new NeechyConfigError(sprintf($format, $link));
        }
    }
}
