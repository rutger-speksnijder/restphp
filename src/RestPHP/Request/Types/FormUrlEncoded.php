<?php
namespace RestPHP\Request\Types;

/**
 * Class to transform form url encoded data into an array.
 *
 * @author Rutger Speksnijder.
 * @since RestPHP 1.0.
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT.
 */
class FormUrlEncoded extends \RestPHP\Request\Request
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
        parse_str(file_get_contents('php://input'), $data);
        if (empty($data) && !empty($_POST)) {
            return $_POST;
        }
        return $data;
    }
}
