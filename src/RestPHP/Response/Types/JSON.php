<?php
namespace RestPHP\Response\Types;

/**
 * Class to transform and show the response in JSON format.
 *
 * @author Rutger Speksnijder.
 * @since RestPHP 1.0.
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT.
 */
class JSON extends \RestPHP\Response\Response
{
    /**
     * The response string.
     * @var string.
     */
    protected $response = '';

    /**
     * The headers for this particular response type.
     * @var array.
     */
    protected $headers = [
        'Content-Type: application/json',
    ];

    /**
     * Transforms the data into a json response.
     *
     * @param mixed $data The data to transform.
     * @param optional array $hypertextRoutes An array with hypertext routes.
     *
     * @return string The transformed response.
     */
    protected function transform($data, $hypertextRoutes = [])
    {
        $links = $this->getHypertextJson($hypertextRoutes);
        if ($links && is_array($data)) {
            $data['_links'] = $links;
        } elseif ($links && !is_array($data)) {
            $data = [$data, '_links' => $links];
        }
        return json_encode($data);
    }

    /**
     * Transforms the hypertext routes into an array for the json string.
     *
     * @param array $routes The hypertext routes.
     *
     * @return array An array of links for the json string.
     */
    private function getHypertextJson($routes = [])
    {
        if (!$routes) {
            return false;
        }
        $links = [];
        foreach ($routes as $name => $route) {
            $links[] = ['rel' => $name, 'href' => $route];
        }
        return $links;
    }
}
