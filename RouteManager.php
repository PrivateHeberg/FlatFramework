<?php

/**
 * Created by PhpStorm.
 * User: Marc Moreau
 * Date: 28/09/2016
 * Time: 04:44
 */
class RouteManager extends AltoRouter
{
    public function __construct( $routes = array(), $basePath = '', $matchTypes = array() ) {
        parent::__construct($routes, $basePath, $matchTypes);
    }
    public function result() {
        // match current request url
        $match = $this->match();
        $error = false;
        if (count($match) != 0) {
            try {
                $fx_data = explode('#', $match['target']);
                if (isset($fx_data[0]) and isset($fx_data[1])) {
                    $obj_name = $fx_data[0];
                    $obj_method = $fx_data[1];
                    $obj = new $obj_name();
                    if (DEBUG == false) ob_clean();
                    call_user_func_array(array($obj, $obj_method), $match['params']);
                } else
                    $error = true;
            } catch (Exception $e) {
                $error = true;
                if (DEBUG) {
                    echo $e;
                }
            }
        } else {
            $error = true;
        }
        if ($error) {
            // no route was matched
            //header('Location:  /404');
        }
    }

}