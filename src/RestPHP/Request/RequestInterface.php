<?php
namespace RestPHP\Request;

/**
 * Request Interface
 *
 * The interface implemented by the Request class.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 2.0.0
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT
 */
interface RequestInterface {

    /**
     * Get data
     *
     * Gets the data.
     *
     * @return array The data.
     */
    function getData();
}
