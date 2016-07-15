<?php
namespace RestPHP\RequestTypes;

/**
 * Form Url Encoded Request
 *
 * Class to transform form url encoded data into an array.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.5.0
 * @package RestPHP/RequestTypes
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
class FormUrlEncodedRequest extends \RestPHP\Request {

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
        parse_str(file_get_contents('php://input'), $data);
        if (empty($data) && !empty($_POST)) {
            return $_POST;
        }
        return $data;
    }
}
