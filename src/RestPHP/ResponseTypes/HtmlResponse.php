<?php
namespace RestPHP\ResponseTypes;

/**
 * Html Response
 *
 * Class to transform and show the response in html format.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.2.0
 * @package RestPHP/ResponseTypes
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
class HtmlResponse extends \RestPHP\Response {

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
     *
     * @return string The transformed response.
     */
    protected function transform($data) {
        return $this->transformToHtml($data);
    }

    /**
     * Transform to html
     *
     * Recursively converts the response into an html string.
     * This is an html string with tables and underlying tables.
     *
     * @param mixed $data The data to transform.
     *
     * @return string The response as an html string.
     */
    private function transformToHtml($data) {
        // Check if the data is not an array
        if (!is_array($data)) {
            return "<p>{$data}</p>";
        }

        $html = "<table style=\"border: 1px solid black;\">";
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                // Recursively transform underlying data
                $html .= "<tr><td style=\"border: 1px solid black; font-weight: bold;\">{$k}:</td><td style=\"border: 1px solid black;\">{$this->transformToHtml($v)}</td></tr>";
            } else {
                $html .= "<tr><td style=\"border: 1px solid black; font-weight: bold;\">{$k}:</td><td style=\"border: 1px solid black;\">{$v}</td></tr>";
            }
        }
        $html .= "</table>";
        return $html;
    }
}
