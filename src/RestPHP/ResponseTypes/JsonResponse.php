<?php
namespace RestPHP\ResponseTypes;

/**
 * Json Response
 *
 * Class to transform and show the response in json format.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.2.0
 * @package RestPHP/ResponseTypes
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
class JsonResponse extends \RestPHP\Response {

    /**
     * The response string.
     * @var string
     */
    protected $response = '';

    /**
     * The headers for this particular response type.
     * @var array
     */
    protected $headers = array(
        'Content-Type: application/json',
    );

    /**
     * Transform
     *
     * Transforms the data into a json response.
     *
     * @param mixed $data The data to transform.
     *
     * @return string The transformed response.
     */
    protected function transform($data) {
        return json_encode($data);
    }
}
