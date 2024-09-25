<?php

require "Router.php";

$route = new Route();
$route
    ->GET('/home', function($request, $response): Response {
        return $response->json(json_encode(array("hello" => "world")));
    })
    ->POST('/home', function($request, $response): Response {
        echo 'You sended a new home °_° !';
    })
->run();
