<?php
require "Router.php";
$router = new Router();
$router
    ->use('/home', function($request, $response){
        return $response->json(json_encode(array("hello" => "bloop")));
    })
    ->post('/home', function($request, $response) {
        return $response->json(json_encode(array("sweet" => "war")));
    })
    ->get('/home', function($request, $response) {
        return $response->json(json_encode(array("hello" => "world")));
    })
->run();
