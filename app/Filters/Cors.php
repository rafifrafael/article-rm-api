<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Cors implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {

        if (array_key_exists('HTTP_ORIGIN', $_SERVER))
        {

            $origin = $_SERVER['HTTP_ORIGIN'];

        }
        else if (array_key_exists('HTTP_REFERER', $_SERVER))
        {

            $origin = $_SERVER['HTTP_REFERER'];

        }
        else
        {

            $origin = $_SERVER['REMOTE_ADDR'];

        }

        $allowed_domains = array(
            'http://localhost:5173/',
            'http://localhost:5173'
        );

        if (in_array($origin, $allowed_domains))
        {

            header('Access-Control-Allow-Origin: ' . $origin);

        }

        header("Access-Control-Allow-Headers: Origin, X-API-KEY, X-Requested-With, X-HTTP-Method-Override, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Headers, authorization, Authorization, observe, enctype, Content-Length, X-Csrf-Token");
        header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");

        $method = $_SERVER['REQUEST_METHOD'];

        if ($method == "OPTIONS") {
            die();
        }

    }
 
    

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
