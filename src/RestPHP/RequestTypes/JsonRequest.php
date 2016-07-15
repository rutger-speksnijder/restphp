<?php
namespace RestPHP\RequestTypes;

/**
 * Json Request
 *
 * Class to transform JSON data into an array.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.5.0
 * @package RestPHP/RequestTypes
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
class JsonRequest extends \RestPHP\Request {

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
        return json_decode(file_get_contents('php://input'), true);
    }
}
