<?php
namespace RestPHP\RequestTypes;

/**
 * Xml Request
 *
 * Class to transform xml data into an array.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.5.0
 * @package RestPHP/RequestTypes
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
class XmlRequest extends \RestPHP\Request {

    /**
     * The request data.
     * @var array
     */
    protected $data = array();

    /**
     * Transform
     *
     * Transforms the data into an array.
     *
     * @return array The array of transformed data.
     */
    protected function transform() {
        // Turn the xml string into an object
        $xml = simplexml_load_string(
            file_get_contents('php://input'),
            'SimpleXMLElement',
            LIBXML_NOCDATA
        );

        // SimpleXMLElement supports encoding the object into a json string.
        // So we encode it and then decode it using the assoc parameter,
        // to get an array with data.
        $json = json_encode($xml);
        return json_decode($json, true);
    }
}
