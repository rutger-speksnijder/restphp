<?php
namespace RestPHP\Response;

/**
 * The interface implemented by the Response class.
 *
 * @author Rutger Speksnijder.
 * @since RestPHP 1.0.
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT.
 */
interface ResponseInterface
{
    /**
     * Gets the response.
     *
     * @return string The response.
     */
    function getResponse();

    /**
     * This method will output the headers set in the headers array.
     *
     * @return object The current object.
     */
    function outputHeaders();

    /**
     * Method to allow casting the object to string,
     * returning the response property.
     *
     * @return string The response.
     */
    function __toString();
}
