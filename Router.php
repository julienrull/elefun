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


class Middleware {
    public ?string  $path;
    public ?string  $method;
    public $callback;
    function __construct(?string $path, ?string $method, $callback) {
        $this->path = $path;
        $this->method = $method;
        $this->callback = $callback;
    }
}

class Route {
    //public array $ROUTES =      ['*' => [], 'GET' => [], 'POST' => [], 'PUT' => [],'DELETE' => []]; 
    public array $MIDDLEWARES = []; 
    public $to_next = false;

    public function next() {
        $this->to_next = true;
    }

    public function middleware(?string $path, ?string $method, $callback): Route { 
        $this->MIDDLEWARES[] = new Middleware($path, $method, $callback);
        //$this->ROUTES[$method] = array($path => $callback);
        return $this; 
    }
    public function get(string $path, $callback): Route { 
        return $this->middleware($path, 'GET', $callback);
    }
    public function post(string $path, $callback): Route { 
        return $this->middleware($path, 'POST', $callback);
    }
    public function use(): Route { 
        if (func_num_args() === 1) {
                return $this->middleware(null, null, func_get_arg(0));
        } else if(func_num_args() === 2) {
                return $this->middleware(func_get_arg(0), null, func_get_arg(1));
        }
        return $this;
    }
    public function run(): void {
        $request = new Request();
        $request->path      = $_SERVER['PATH_INFO'];
        $request->method    = $_SERVER['REQUEST_METHOD'];
        $request->params    = $_GET;
        $request->form      = $_POST;
        $response = new Response();
        foreach($this->MIDDLEWARES as $middleware) {
            if($middleware->path === null) {
                call_user_func($middleware->callback, $request, $response, array($this, 'next'));
            }else if($middleware->path === $request->path) {
                if($middleware->method === null){
                    call_user_func($middleware->callback, $request, $response, array($this, 'next'));
                }else if($middleware->method === $request->method) {
                    call_user_func($middleware->callback, $request, $response, array($this, 'next'));
                }
            }
            if(!$this->to_next) {
                break;
            }
            $this->to_next = false;
        }
        echo $response->body;
    } 
}
