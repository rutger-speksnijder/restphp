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
     * @param optional array $hypertextRoutes An array with hypertext routes.
     *
     * @return string The transformed response.
     */
    protected function transform($data, $hypertextRoutes = array()) {
        $links = $this->getHypertextJson($hypertextRoutes);
        if ($links && is_array($data)) {
            $data['_links'] = $links;
        } elseif ($links && !is_array($data)) {
            $data = array($data, '_links' => $links);
        }
        return json_encode($data);
    }

    /**
     * Get hypertext json
     *
     * Transforms the hypertext routes into an array for the json string.
     *
     * @param array $routes The hypertext routes.
     *
     * @return array An array of links for the json string.
     */
    private function getHypertextJson($routes = array()) {
        if (!$routes) {
            return false;
        }
        $links = array();
        foreach ($routes as $name => $route) {
            $links[] = array('rel' => $name, 'href' => $route);
        }
        return $links;
    }
}
