<?php
namespace RestPHP\Request\Types;

/**
 * Get
 *
 * Class to transform GET data into an array.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 2.0.0
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT
 */
class Get extends \RestPHP\Request\Request {

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
