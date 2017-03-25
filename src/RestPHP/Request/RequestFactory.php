<?php
namespace RestPHP\Request;

/**
 * Factory for creating a request object.
 *
 * @author Rutger Speksnijder.
 * @since RestPHP 1.0.
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT.
 */
class RequestFactory
{
    /**
     * The array with accepted content types.
     * @var array.
     */
    private $types;

    /**
     * Constructs a new instance of the Request Factory class
     * and sets the types array.
     */
    public function __construct()
    {
        $this->types = array(
            // GET
            '' => __NAMESPACE__ . '\\Types\\GET',
            'text/plain' => __NAMESPACE__ . '\\Types\\GET',

            // Form url encoded
            'application/x-www-form-urlencoded' => __NAMESPACE__ . '\\Types\\FormUrlEncoded',

            // XML
            'text/xml' => __NAMESPACE__ . '\\Types\\XML',
            'application/xml' => __NAMESPACE__ . '\\Types\\XML',

            // JSON
            'application/json' => __NAMESPACE__ . '\\Types\\JSON',
        );
    }

    /**
     * Builds the request object.
     *
     * @param string $type The type of request to build.
     *
     * @throws Exception Throws an exception for unknown request types.
     *
     * @return \RestPHP\Request The created object.
     */
    public function build($type)
    {
        if (!isset($this->types[$type])) {
            throw new \Exception("Unknown request type.");
        }
        return new $this->types[$type]();
    }

    /**
     * Checks if the request type is a supported request type.
     *
     * @param string $type The request type.
     *
     * @return boolean True if the type is supported, false otherwise.
     */
    public function isSupported($type)
    {
        return isset($this->types[$type]);
    }
}
