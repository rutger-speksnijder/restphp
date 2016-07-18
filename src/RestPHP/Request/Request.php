<?php
namespace RestPHP\Request;

/**
 * Request
 *
 * Abstract class to extend from when creating request types.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 2.0.0
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT
 */
abstract class Request implements \RestPHP\Request\RequestInterface {

    /**
     * The request data.
     * @var array
     */
    protected $data = array();

    /**
     * Construct
     *
     * Constructs the new request class
     * and sets the data property to the
     * transformed and cleaned data.
     */
    final public function __construct() {
        $this->data = $this->cleanInputs($this->transform());
    }

    /**
     * Transform
     *
     * This method transforms the request into an array with data.
     * This method should return the transformed data.
     *
     * @return array The array of transformed data.
     */
    abstract protected function transform();

    /**
     * Get data
     *
     * Gets the data.
     *
     * @return array The data.
     */
    final public function getData() {
        return $this->data;
    }

    /**
     * Clean inputs
     *
     * Cleans an array with input data.
     *
     * @param array $data The input data.
     *
     * @return array A sanitized array.
     */
    final protected function cleanInputs($data) {
        $cleanInput = [];
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $cleanInput[$key] = $this->cleanInputs($value);
            }
        } else {
            $cleanInput = trim(strip_tags($data));
        }
        return $cleanInput;
    }
}
