<?php
/**
 * core/neechy/errors.php
 *
 * Neechy exception classes
 *
 */
#
# Errors
#
class NeechyError extends Exception {

    public $status_code = 500;

    public function __construct($message, $code=0) {
        parent::__construct($message, $code);
        $this->status_code = $code;
    }

    public function __toString() {
        return sprintf("%s: %s\n", __CLASS__, $this->message);
    }
}

class NeechyWebServiceError extends NeechyError {}

class NeechyConsoleError extends NeechyError {}

class NeechyCsrfError extends NeechyWebServiceError {}

class NeechyHandlerError extends NeechyWebServiceError {}
