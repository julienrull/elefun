<?php
require "Router.php";

$router = new Router();
$router
    ->use(function(&$req, &$res, $next){
        $res->text("Try commenting the line below...");
        $next();
    })
    ->post('/home', function(&$req, &$res) {
        $res->json(json_encode(array("sweet" => "war")));
    })
    ->get('/home', function(&$req, &$res) {
        $res->json(json_encode(array("hello" => "world")));
    })
->run();
