<?php
require "Router.php";


$router = new Router();
$router
    ->use(function(&$req, &$res, $next){
        error_log(print_r("GLOBAL use 1", TRUE)); 
        $next();
    })
    ->post('/home', function(&$req, &$res) {
        $res->json(json_encode(array("sweet" => "war")));
    })
    ->get('/home', function(&$req, &$res) {
        $res->json(json_encode(array("hello" => "world")));
    })
->run();
