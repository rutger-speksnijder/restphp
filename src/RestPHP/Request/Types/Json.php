<?php
namespace RestPHP\Request\Types;

/**
 * Class to transform JSON data into an array.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 2.0.0
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT
 */
class Json extends \RestPHP\Request\Request
{
    /**
     * The request data.
     * @var array
     */
    protected $data = array();

    /**
     * Transforms the data into an array.
     *
     * @return array The array of transformed data.
     */
    protected function transform()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
}
