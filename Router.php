<?php
class Request {
    public string   $path;
    public string   $method;
    public array    $params;
    public array    $form;
}
class Response {
    public int     $status;
    public mixed   $body;
    function __construct() {
        $this->body = null;
        $this->status = 200;
        $this->header('Content-Type', 'text/plain; charset=utf-8');
    }

    public function header(string $key, string $value): Response{
        header("$key: $value");
        return $this;
    }

    public function status(int $code): Response{
        http_response_code($code);
        return $this;
    }
    public function send(string $body): Response{
        $this->body = $body;
        return $this;
    }
    public function text(string $body): Response{
        $this->header('Content-Type', 'text/plain; charset=utf-8');
        $this->body = $body;
        return $this;
    }
    public function html(string $body): Response{
        $this->header('Content-Type', 'text/html; charset=utf-8');
        $this->body = $body;
        return $this;
    }
    public function json(string $body): Response{
        $this->header('Content-Type', 'application/json; charset=utf-8');
        $this->body = $body;
        return $this;
    }
}
class Route {
    public array $ROUTES =      ['*' => [], 'GET' => [], 'POST' => [], 'PUT' => [],'DELETE' => [],]; 
    public array $MIDDLEWARES = ['*' => [], 'GET' => [], 'POST' => [], 'PUT' => [],'DELETE' => [],]; 

    public function CUSTOM (string $method, string $path, $callback): Route { 
        $this->ROUTES[$method] = array($path => $callback);
        return $this; 
    }
    public function GET (string $path, $callback): Route { 
        return $this->CUSTOM('GET', $path, $callback);
    }
    public function POST (string $path, $callback): Route { 
        return $this->CUSTOM('POST', $path, $callback);
    }
    public function run(): void {
        $request = new Request();
        $request->path      = $_SERVER['PATH_INFO'];
        $request->method    = $_SERVER['REQUEST_METHOD'];
        $request->params    = $_GET;
        $request->form      = $_POST;
        $response = new Response();
        if($this->ROUTES[$request->method][$request->path] !== null) {
            $response = $this->ROUTES[$request->method][$request->path]($request, $response);
            echo $response->body;
        }
    } 
}
