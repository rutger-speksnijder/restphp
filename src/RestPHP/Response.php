<?php
namespace RestPHP;

/**
 * Response
 *
 * Abstract class to extend from when creating response types.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.2.0
 * @package RestPHP
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
abstract class Response {

    /**
     * Supported response types.
     * Change this array if you add more response types.
     * @var array
     */
    public static $supportedTypes = array(
        'html' => '\\RestPHP\\ResponseTypes\\HtmlResponse',
        'xml' => '\\RestPHP\\ResponseTypes\\XmlResponse',
        'json' => '\\RestPHP\\ResponseTypes\\JsonResponse',
        'text' => '\\RestPHP\\ResponseTypes\\TextResponse',
    );

    /**
     * Supported accept headers.
     * Change this array if you add more response types.
     * @var array
     */
    public static $supportedAcceptHeaders = array(
        // HTML headers
        'text/html' => 'html',

        // XML headers
        'text/xml' => 'xml',
        'application/xml' => 'xml',

        // JSON headers
        'application/json' => 'json',

        // Text headers
        'text/plain' => 'text',
    );

    /**
     * The response string.
     * @var string
     */
    protected $response = '';

    /**
     * The headers for this particular response type.
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
     *
     * @return object The new response class.
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
     * @return object The current object.
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

    /**
     * Create response
     *
     * Creates the response according to the specified type.
     *
     * @param string $type The response type.
     * @param mixed $data The response data.
     * @param optional array $hypertextRoutes An array with hypertext routes.
     *
     * @return object The new response object.
     */
    final public static function createResponse($type, $data, $hypertextRoutes = array()) {
        if (isset(self::$supportedTypes[$type])) {
            return new self::$supportedTypes[$type]($data, $hypertextRoutes);
        }
        return new self::$supportedTypes['text']($data, $hypertextRoutes);
    }

    /**
     * Parse accept header
     *
     * Gets the first value in the accept header
     * and parses it to a content type.
     *
     * @return string The parsed content type.
     */
    final public static function parseAcceptHeader() {
        if (!isset($_SERVER['HTTP_ACCEPT']) || trim($_SERVER['HTTP_ACCEPT']) == '') {
            return '';
        }
        $value = explode(',', $_SERVER['HTTP_ACCEPT'])[0];
        return (isset(self::$supportedAcceptHeaders[$value]) ?
            self::$supportedAcceptHeaders[$value] : 'text');
    }
}
