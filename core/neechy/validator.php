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
    public $options = null;
    public $valid = array();
    public $errors = array();

    #
    # Constructor
    #
    public function __construct($options=array()) {
        $this->options = $options;
    }

    #
    # Public Methods
    #
    public function validate() {
        throw new Exception('Override in subclass');
    }

    public function is_valid() {
        return (! $this->has_errors());
    }

    public function option($key) {
        if ( ! isset($this->options[$key]) ) {
            return null;
        }
        else {
            return $this->options[$key];
        }
    }

    public function field_value($field) {
        if ( ! isset($this->valid[$field]) ) {
            return '';
        }
        else {
            return $this->valid[$field];
        }
    }

    public function set_valid_field_value($field, $value) {
        $this->valid[$field] = $value;
    }

    public function has_errors() {
        return $this->count_errors() > 0;
    }

    public function has_error($field) {
        return isset($this->errors[$field]) && count($this->errors[$field]) > 0;
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
    # Protected Methods
    #
    protected function validate_field_present($field, $message=null) {
        $value = $this->request->post($field, '');
        $message = ( $message ) ? $message : format('%s required', $field);

        if ( $this->string_is_empty($value) ) {
            $this->add_error($field, $message);
            return false;
        }
        else {
            return true;
        }
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
