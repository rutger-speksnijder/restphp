<?php
namespace RestPHP\Response\Types;

/**
 * Class to transform and show the response in xml format.
 *
 * @author Rutger Speksnijder.
 * @since RestPHP 1.0.
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT.
 */
class XML extends \RestPHP\Response\Response
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
        'Content-Type: application/xml',
    ];

    /**
     * Transforms the data into an xml response.
     *
     * @param mixed $data The data to transform.
     * @param optional array $hypertextRoutes An array with hypertext routes.
     *
     * @return string The transformed response.
     */
    protected function transform($data, $hypertextRoutes = [])
    {
        return $this->transformToXml($data, $hypertextRoutes);
    }

    /**
     * Recursively converts the response into an xml string.
     *
     * @param mixed $data The data to transform.
     * @param optional array $hypertextRoutes An array with hypertext routes.
     *
     * @return string The response as an xml string.
     */
     private function transformToXml($data = [], $hypertextRoutes = [], $depth = 0)
     {
        // Set the xml string
        $xml = '';

        // Starting depth, so add the xml starting lines
        if ($depth === 0) {
            $xml = "<?xml version=\"1.0\"?>\n";
            $xml .= "<response>\n";
        }

        // Check if data is an array. If not, return a response with data to string.
        if (!is_array($data)) {
            return "<?xml version=\"1.0\"?>\n<response>{$data}{$this->getHypertextXml($hypertextRoutes)}</response>";
        }

        // Loop through the data
        foreach ($data as $k => $v) {
            // Add tabs
            for ($i = 0; $i < $depth; $i++) {
                $xml .= "\t";
            }

            // Check if key is a number.
            // - XML doesn't allow <number></number> tags.
            $key = $k;
            if (is_int($k) || (int)$k > 0) {
                $key = "key_{$k}";
            }

            // Check if the value is an array. If so, generate xml for it.
            if (is_array($v)) {
                $xml .= "<{$key}>\n{$this->transformToXml($v, [], $depth+1)}\n</{$key}>\n";
            } else {
                $xml .= "<{$key}>{$v}</{$key}>\n";
            }
        }

        // Add the closing tag if this is depth 0
        if ($depth === 0) {
            $xml .= $this->getHypertextXml($hypertextRoutes);
            $xml .= "</response>\n";
        }

        // Return the xml string
        return $xml;
    }

    /**
     * Generates the xml for the hypertext routes.
     *
     * @param optional array $routes The hypertext routes.
     *
     * @return string The hypertext routes xml string.
     */
    private function getHypertextXml($routes = [])
    {
        // Check if we have routes
        if (!$routes) {
            return '';
        }

        // Loop through the routes and add them as links to the xml
        $xml = '';
        foreach ($routes as $name => $route) {
            $xml .= "<link rel=\"{$name}\" href=\"{$route}\"/>\n";
        }
        return $xml;
    }
}
