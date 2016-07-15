<?php
namespace RestPHP\RequestTypes;

/**
 * Get Request
 *
 * Class to transform GET data into an array.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.5.0
 * @package RestPHP/RequestTypes
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
class GetRequest extends \RestPHP\Request {

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
        return $_GET;
    }
}
