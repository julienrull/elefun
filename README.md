# Elefun

A fun and simple ExpressJS like router for PHP.

## Getting Started

### Installation

Download "Elefun.php" with your tool of choice:

```bash
curl  https://raw.githubusercontent.com/julienrull/elefun/refs/heads/master/Elefun.php -o Elefun.php

# OR

wget https://raw.githubusercontent.com/julienrull/elefun/refs/heads/master/Elefun.php  -o Elefun.php
```

### Usage 
```php
<?php
// File: "index.php"

// 1. Import module
require "Elefun.php";

// 2. Create a router
$router = new Router();

// 3. Define your routes and middlewares
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
->run(); // 4. Run the router that will catch all incomming requests
```
