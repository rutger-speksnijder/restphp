<?php
namespace RestPHP\Request;

/**
 * Abstract class to extend from when creating request types.
 *
 * @author Rutger Speksnijder.
 * @since RestPHP 1.0.
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT.
 */
abstract class Request implements \RestPHP\Request\RequestInterface
{
    /**
     * The request data.
     * @var array.
     */
    protected $data = [];

    /**
     * Constructs the new request class and sanitizes the data.
     */
    final public function __construct()
    {
        $this->data = $this->sanitize($this->transform());
    }

    /**
     * This method transforms the request into an array with data.
     * This method should return the transformed data.
     *
     * @return array The array of transformed data.
     */
    abstract protected function transform();

    /**
     * Gets the data.
     *
     * @return array The data.
     */
    final public function getData()
    {
        return $this->data;
    }

    /**
     * Sanitizes an array with data.
     *
     * @param array $data The input data.
     *
     * @return array A sanitized array.
     */
    final protected function sanitize($data)
    {
        // Recursively loop through the data array and return the sanitized data
        $sanitized = [];
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $sanitized[$key] = $this->sanitize($value);
            }
        } else {
            $sanitized = trim(strip_tags($data));
        }
        return $sanitized;
    }
}
