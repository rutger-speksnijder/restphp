<?php
namespace RestPHP;

/**
 * Request
 *
 * Abstract class to extend from when creating request types.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.5.0
 * @package RestPHP
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
abstract class Request {

    /**
     * Supported request types.
     * Change this array if you add more request types.
     * @var array
     */
    public static $supportedTypes = array(
        'get' => '\\RestPHP\\RequestTypes\\GetRequest',
        'form-urlencoded' => '\\RestPHP\\RequestTypes\\FormUrlEncodedRequest',
        'xml' => '\\RestPHP\\RequestTypes\\XmlRequest',
        'json' => '\\RestPHP\\RequestTypes\\JsonRequest',
    );

    /**
     * Supported content-type headers.
     * Change this array if you add more request types.
     * @var array
     */
    public static $supportedContentTypeHeaders = array(
        // Form request
        'application/x-www-form-urlencoded' => 'form-urlencoded',

        // Xml requests
        'application/xml' => 'xml',
        'text/xml' => 'xml',

        // Json request
        'application/json' => 'json',

        // Get request
        '' => 'get',
    );

    /**
     * The request data.
     * @var array
     */
    protected $data = array();

    /**
     * Construct
     *
     * Constructs the new request class.
     *
     * @return object The new request class.
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

    /**
     * Create request
     *
     * Creates the request according to the request type.
     *
     * @return mixed The new request object if supported, false otherwise.
     */
    final public static function createRequest() {
        $type = self::parseContentTypeHeader();
        if (!isset(self::$supportedTypes[$type])) {
            return false;
        }
        return new self::$supportedTypes[$type]();
    }

    /**
     * Parse content type header
     *
     * Parses the content type header and turns it into the request type.
     *
     * @return string The request type.
     */
    final public static function parseContentTypeHeader() {
        if (!isset($_SERVER['CONTENT_TYPE']) || trim($_SERVER['CONTENT_TYPE']) == '') {
            return self::$supportedContentTypeHeaders[''];
        }
        $value = explode(';', $_SERVER['CONTENT_TYPE'])[0];
        return (isset(self::$supportedContentTypeHeaders[$value]) ?
            self::$supportedContentTypeHeaders[$value] : self::$supportedContentTypeHeaders['']);
    }
}
