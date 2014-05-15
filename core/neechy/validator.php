<?php
/**
 * core/neechy/validator.php
 *
 * Base Neechy Validator class
 *
 */


class NeechyValidator {

    #
    # Properties
    #
    public $request = null;
    public $errors = array();

    #
    # Constructor
    #
    public function __construct($request=NULL) {
        $this->request = $request;
    }

    #
    # Public Methods
    #
    public function is_valid() {
        return (! $this->has_errors);
    }

    public function has_errors() {
        return $this->count_errors() > 0;
    }

    public function add_error($field, $message) {
        if ( isset($this->errors[$field]) ) {
            $this->errors[$field][] = $message;
        }
        else {
            $this->errors[$field] = array($message);
        }

        return $this->errors;
    }

    public function string_is_empty($value) {
        return strlen($value) < 1;
    }

    public function is_empty($value) {
        # Careful: '0' is true. Use string_is_empty.
        return empty($value);
    }

    public function values_match($value1, $value2) {
        return $value1 == $value2;
    }

    public function string_is_too_short($value, $min_length) {
        return strlen($value) < $min_length;
    }

    public function is_valid_email($address) {
        return (bool) filter_var($address, FILTER_VALIDATE_EMAIL);
    }


    #
    # Private Methods
    #
    protected function count_errors() {
        $count = 0;
        foreach ( $this->errors as $field => $messages ) {
            $count += count($messages);
        }
        return $count;
    }
}
