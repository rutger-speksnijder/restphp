<?php
namespace RestPHP\ResponseTypes;

/**
 * Text Response
 *
 * Class to transform and show the response in text format.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.2.0
 * @package RestPHP/ResponseTypes
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
class TextResponse extends \RestPHP\Response {

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
     *
     * @return string The transformed response.
     */
    protected function transform($data) {
        return $this->transformToText($data);
    }

    /**
     * Transform to text
     *
     * Recursively converts the response into a text string.
     *
     * @param mixed $data The data to transform.
     *
     * @return string The response as a text string.
     */
    private function transformToText($data, $depth = 0) {
        // Return the data as string if it's not an array
        if (!is_array($data)) {
            return (string)$data;
        }

        // Loop through the data and add to the string
        $str = '';
        foreach ($data as $k => $v) {
            $str .= "\n";

            // Add tabs
            for ($i = 0; $i < $depth; $i++) {
                $str.="\t";
            }

            if (is_array($v)) {
                // Recursively transform underlying data
                $str .= "{$k}: {$this->transformToText($v, $depth+1)}\n";
            } else {
                $str .= "{$k}: {$v}\n";
            }
        }
        return $str;
    }
}
