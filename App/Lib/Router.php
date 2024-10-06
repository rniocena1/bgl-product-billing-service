<?php namespace App\Lib;

class Router
{
    /**
     * Function for the GET route
     *
     * @param $route
     * @param $callback
     * @return void
     */
    public static function get($route, $callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
            return;
        }

        self::on($route, $callback);
    }

    /**
     * Function for the POST route
     *
     * @param $route
     * @param $callback
     * @return void
     */
    public static function post($route, $callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            return;
        }

        self::on($route, $callback);
    }

    /**
     * Function for getting the request data
     *
     * @param [type] $regex
     * @param [type] $callback
     * @return void
     */
    public static function on($regex, $callback)
    {
        $params = $_SERVER['REQUEST_URI'];
        $params = (stripos($params, "/") !== 0) ? "/" . $params : $params;
        $regex = str_replace('/', '\/', $regex);
        $is_match = preg_match('/^' . ($regex) . '$/', $params, $matches, PREG_OFFSET_CAPTURE);

        if ($is_match) {
            // first value is normally the route, lets remove it
            array_shift($matches);
            // Get the matches as parameters
            $params = array_map(function ($param) {
                return $param[0];
            }, $matches);
            $callback(new Request($params), new Response());
        }
    }
}