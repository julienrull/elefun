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

class Router {
    //public array $ROUTES =      ['*' => [], 'GET' => [], 'POST' => [], 'PUT' => [],'DELETE' => []]; 
    public array $MIDDLEWARES = []; 
    public bool    $to_next;

    public function next(){
        $this->to_next = true;
    }

    public function middleware(?string $path, ?string $method, $callback): Router { 
        $this->MIDDLEWARES[] = new Middleware($path, $method, $callback);
        return $this; 
    }
    public function get(string $path, ...$callbacks): Router { 
        return $this->use($path, 'GET', $callbacks);
    }
    public function post(string $path, ...$callbacks): Router { 
        return $this->use($path, 'POST', $callbacks);
    }
    public function put(string $path, ...$callbacks): Router { 
        return $this->use($path, 'PUT', $callbacks);
    }
    public function delete(string $path, ...$callbacks): Router { 
        return $this->use($path, 'DELETE', $callbacks);
    }
    public function patch(string $path, ...$callbacks): Router { 
        return $this->use($path, 'PATCH', $callbacks);
    }
    public function use(...$args): Router { 
        $path = null;
        $method = null;
        $callbacks_index = 0;
        if(gettype($args[0]) === 'string'){
            $path = $args[0];
            $callbacks_index = 1;
            if(gettype($args[1]) === 'string'){
                $method = $args[1];
                $callbacks_index = 2;
            }        
        } else if(gettype($args[0]) !== 'object') {
            throw new ErrorException('Bad function usage, function use: function use(string $path, string $method, ...$middleware_callbacks), function use(string $path, ...$middleware_callbacks), function use(...$middleware_callbacks)');
        }
        for($i=$callbacks_index;$i<sizeof($args);$i++){
            if(gettype($args[$i]) === 'object'){
                $this->middleware($path, $method, $args[$i]);
            }else if(gettype($args[$i]) === 'array'){
                foreach($args[$i] as $cb) {
                    $this->middleware($path, $method, $cb);
                }
            }
        }
        return $this;
    }
    public function run(): void {
        $request = new Request();
        $this->to_next = true;
        $request->path      = $_SERVER['PATH_INFO'];
        $request->method    = $_SERVER['REQUEST_METHOD'];
        $request->params    = $_GET;
        $request->form      = $_POST;
        $response = new Response();
        foreach($this->MIDDLEWARES as $middleware) {
            if(
                $this->to_next && 
                ($middleware->path === null || $middleware->path === $request->path) && 
                ($middleware->method === null || $middleware->method === $request->method)
            ) 
            {
                    $this->to_next = false;
                    error_log(print_r($this->to_next, TRUE)); 
                    call_user_func_array($middleware->callback, array(&$request, &$response, array($this, 'next')));
            }
        }
        echo $response->body;
    } 
}


