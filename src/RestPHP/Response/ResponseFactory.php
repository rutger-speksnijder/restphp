<?php
namespace RestPHP\Response;

/**
 * Response Factory
 *
 * Factory for creating a response object.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 2.0.0
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT
 */
class ResponseFactory {

    /**
     * The array with accepted content types.
     * @var array
     */
    private $types;

    /**
     * Construct
     *
     * Constructs a new instance of the Response Factory class
     * and sets the types array.
     */
    public function __construct() {
        $this->types = array(
            'text/html' => __NAMESPACE__ . '\\Types\\Html',

            'text/xml' => __NAMESPACE__ . '\\Types\\Xml',
            'application/xml' => __NAMESPACE__ . '\\Types\\Xml',

            'application/json' => __NAMESPACE__ . '\\Types\\Json',

            'text/plain' => __NAMESPACE__ . '\\Types\\Text',
        );
    }

    /**
     * Build
     *
     * Builds the response object.
     *
     * @param string $type The type of response to build.
     * @param mixed $data The response data.
     * @param optional array $hypertextRoutes An array of hypertext routes.
     *
     * @throws Exception Throws an exception for unknown response types.
     *
     * @return \RestPHP\Response The created object.
     */
    public function build($type, $data, $hypertextRoutes = array()) {
        if (!isset($this->types[$type])) {
            throw new \Exception("Unknown response type.");
        }
        return new $this->types[$type]($data, $hypertextRoutes);
    }

    /**
     * Is supported
     *
     * Checks if the response type is a supported response type.
     *
     * @param string $type The response type.
     *
     * @return boolean True if the type is supported, false otherwise.
     */
    public function isSupported($type) {
        return isset($this->types[$type]);
    }
}