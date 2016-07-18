<?php
namespace RestPHP\Response\Types;

/**
 * Text
 *
 * Class to transform and show the response in text format.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 2.0.0
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT
 */
class Text extends \RestPHP\Response\Response {

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
        'Content-Type: text/plain',
    );

    /**
     * Transform
     *
     * Transforms the data into a text response.
     *
     * @param mixed $data The data to transform.
     * @param optional array $hypertextRoutes An array with hypertext routes.
     *
     * @return string The transformed response.
     */
    protected function transform($data, $hypertextRoutes = array()) {
        return $this->transformToText($data, $hypertextRoutes);
    }

    /**
     * Transform to text
     *
     * Recursively converts the response into a text string.
     *
     * @param mixed $data The data to transform.
     * @param optional array $hypertextRoutes An array with hypertext routes.
     *
     * @return string The response as a text string.
     */
    private function transformToText($data, $hypertextRoutes = array(), $depth = 0) {
        // Return the data as string if it's not an array
        if (!is_array($data)) {
            return "{$data}\n{$this->getHypertextString($hypertextRoutes)}";
        }

        // Loop through the data and add to the string
        $str = '';
        foreach ($data as $k => $v) {
            // Add tabs
            for ($i = 0; $i < $depth; $i++) {
                $str.="\t";
            }

            if (is_array($v)) {
                // Recursively transform underlying data
                $str .= "{$k}: {$this->transformToText($v, array(), $depth+1)}\n";
            } else {
                $str .= "{$k}: {$v}\n";
            }
        }

        // Add the hypertext routes
        $str .= $this->getHypertextString($hypertextRoutes);
        return $str;
    }

    /**
     * Get hypertext string
     *
     * Transforms the hypertext routes into a string.
     *
     * @param array $routes The hypertext routes.
     *
     * @return string The hypertext routes transformed into a string.
     */
    private function getHypertextString($routes = array()) {
        // Check if we have routes
        if (!$routes) {
            return '';
        }

        // Loop through the routes and add them to the string
        $str = "links: \n";
        foreach ($routes as $name => $route) {
            $str .= "\trel: {$name}. href: {$route}.\n";
        }
        return $str;
    }
}