<?php
namespace RestPHP\Response;

/**
 * Response
 *
 * Abstract class to extend from when creating response types.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 2.0.0
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT
 */
abstract class Response implements \RestPHP\Response\ResponseInterface {

    /**
     * The response string.
     * @var string
     */
    protected $response = '';

    /**
     * The headers to send for this particular response type.
     * @var array
     */
    protected $headers = array();

    /**
     * Construct
     *
     * Constructs the new response class.
     *
     * @param mixed $data The response data.
     * @param optional array $hypertextRoutes An array of hypertext routes.
     */
    final public function __construct($data, $hypertextRoutes = array()) {
        $this->response = $this->transform($data, $hypertextRoutes);
    }

    /**
     * Transform
     *
     * This method takes an array or string of data
     * and transforms it into the appropriate response type.
     * This method should return the transformed response data
     * with the hypertext routes the client can follow added to it.
     *
     * @param mixed $data The data to transform.
     * @param optional array $hypertextRoutes An array of hypertext routes.
     *
     * @return string The transformed response data.
     */
    abstract protected function transform($data, $hypertextRoutes = array());

    /**
     * Get response
     *
     * Gets the response.
     *
     * @return string The response.
     */
    final public function getResponse() {
        return $this->response;
    }

    /**
     * Output headers
     *
     * This method will output the headers set in the headers array.
     *
     * @return $this The current object.
     */
    final public function outputHeaders() {
        foreach ($this->headers as $header) {
            header($header);
        }
        return $this;
    }

    /**
     * To string
     *
     * Method to allow casting the object to string,
     * returning the response property.
     *
     * @return string The response.
     */
    final public function __toString() {
        return $this->response;
    }
}
