<?php
namespace RestPHP\Response\Types;

/**
 * Html
 *
 * Class to transform and show the response in html format.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 2.0.0
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT
 */
class Html extends \RestPHP\Response\Response {

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
        'Content-Type: text/html',
    );

    /**
     * Transform
     *
     * Transforms the data into an html response.
     *
     * @param mixed $data The data to transform.
     * @param optional array $hypertextRoutes An array with hypertext routes.
     *
     * @return string The transformed response.
     */
    protected function transform($data, $hypertextRoutes = array()) {
        return $this->transformToHtml($data, $hypertextRoutes);
    }

    /**
     * Transform to html
     *
     * Recursively converts the response into an html string.
     * This is an html string with tables and underlying tables.
     *
     * @param mixed $data The data to transform.
     * @param optional array $hypertextRoutes An array with hypertext routes.
     *
     * @return string The response as an html string.
     */
    private function transformToHtml($data, $hypertextRoutes = array()) {
        // Generate the html for the hypertext routes
        $hypertextHtml = $this->getHypertextHtml($hypertextRoutes);

        // Check if the data is not an array
        if (!is_array($data)) {
            return "<p>{$data}</p>\n{$hypertextHtml}";
        }

        // Generate the html table
        $html = "<table style=\"border: 1px solid black;\">";
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                // Recursively transform underlying data
                $html .= "<tr><td style=\"border: 1px solid black; font-weight: bold;\">{$k}:</td><td style=\"border: 1px solid black;\">{$this->transformToHtml($v)}</td></tr>";
            } else {
                $html .= "<tr><td style=\"border: 1px solid black; font-weight: bold;\">{$k}:</td><td style=\"border: 1px solid black;\">{$v}</td></tr>";
            }
        }

        // Add the hypertext html
        $html .= "<tr><td style=\"border: 1px solid black; font-weight: bold;\">Hypertext:</td><td style=\"border: 1px solid black;\">{$hypertextHtml}</td></tr>";
        $html .= "</table>";
        return $html;
    }

    /**
     * Get hypertext html
     *
     * Generates the html for the hypertext routes.
     *
     * @param optional array $routes The hypertext routes.
     *
     * @return string The hypertext routes html table.
     */
    private function getHypertextHtml($routes = array()) {
        // Check if we have routes
        if (!$routes) {
            return '';
        }

        // Create the html table
        $html = "<table style=\"border: 1px solid black;\">\n";

        // Loop through the routes and add them as rows to the table
        foreach ($routes as $name => $route) {
            $html .= "<tr>\n";
            $html .= "<td style=\"border: 1px solid black;\">rel: {$name}</td>\n";
            $html .= "<td style=\"border: 1px solid black;\">href: {$route}</td>\n";
            $html .= "</tr>\n";
        }
        $html .= "</table>\n";

        return $html;
    }
}
