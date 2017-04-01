<?php
namespace RestPHP\Request\Types;

/**
 * Class to transform GET data into an array.
 *
 * @author Rutger Speksnijder.
 * @since RestPHP 1.0.
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT.
 */
class GET extends \RestPHP\Request\Request
{
    /**
     * The request data.
     * @var array.
     */
    protected $data = [];

    /**
     * Transforms the data into an array.
     *
     * @return array The array of transformed data.
     */
    protected function transform()
    {
        return $_GET;
    }
}
