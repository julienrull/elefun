<?php
require "Router.php";
$route = new Route();
$route
    ->use(function($request, $response, $next){
        $next();
    })
    ->get('/home', function($request, $response, $next): Response {
        return $response->json(json_encode(array("hello" => "world")));
    })
    ->post('/home', function($request, $response, $next): Response {
        return $response->json(json_encode(array("hello" => "world")));
    })
->run();
