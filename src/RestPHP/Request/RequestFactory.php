<?php
namespace RestPHP\Request;

/**
 * Request Factory
 *
 * Factory for creating a request object.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 2.0.0
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT
 */
class RequestFactory {

    /**
     * The array with accepted content types.
     * @var array
     */
    private $types;

    /**
     * Construct
     *
     * Constructs a new instance of the Request Factory class
     * and sets the types array.
     */
    public function __construct() {
        $this->types = array(
            '' => __NAMESPACE__ . '\\Types\\Get',

            'application/x-www-form-urlencoded' => __NAMESPACE__ . '\\Types\\FormUrlEncoded',

            'text/xml' => __NAMESPACE__ . '\\Types\\Xml',
            'application/xml' => __NAMESPACE__ . '\\Types\\Xml',

            'application/json' => __NAMESPACE__ . '\\Types\\Json',
        );
    }

    /**
     * Build
     *
     * Builds the request object.
     *
     * @param string $type The type of request to build.
     *
     * @throws Exception Throws an exception for unknown request types.
     *
     * @return \RestPHP\Request The created object.
     */
    public function build($type) {
        if (!isset($this->types[$type])) {
            throw new \Exception("Unknown request type.");
        }
        return new $this->types[$type]();
    }

    /**
     * Is supported
     *
     * Checks if the request type is a supported request type.
     *
     * @param string $type The request type.
     *
     * @return boolean True if the type is supported, false otherwise.
     */
    public function isSupported($type) {
        return isset($this->types[$type]);
    }
}