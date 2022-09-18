<?php

namespace ExpressRouter;

class Request {
    public ?Array $parameters = null;
    public ?Array $query = null;
    public $body = null;
    public string $route = "";
    public string $method = "";
}

class Response {
    public int $status_code = 200;
    public Array $response = Array();

    public function status(int $new_status_code)
    {
        $this->status_code = $new_status_code;
        return $this;
    }

    public function response(Array $response)
    {
        $this->response = $response;
        return $this;
    }
}

class Router
{
    private Array $routes_map = Array(
        "route" => "",
        "handlers" => Array(),
        "routes" => Array()
    );

    function get(string $route, $callback)
    {
        $this->register_route($route, "GET", $callback);
    }

    function head(string $route, $callback)
    {
        $this->register_route($route, "HEAD", $callback);
    }

    function put(string $route, $callback)
    {
        $this->register_route($route, "PUT", $callback);
    }

    function delete(string $route, $callback)
    {
        $this->register_route($route, "DELETE", $callback);
    }

    function post(string $route, $callback)
    {
        $this->register_route($route, "POST", $callback);
    }

    private function register_route(string $route, string $method, $callback)
    {
        $route_parts = explode("/", $route);
        $current_route_map = &$this->routes_map;
        foreach ($route_parts as $route_part) {
            if($route_part === "") continue;
            if(!array_key_exists($route_part, $current_route_map["routes"]))
                $current_route_map["routes"][$route_part] = Array(
                    "route" => $current_route_map["route"]."/".$route_part,
                    "handlers" => Array(),
                    "routes" => Array()
                );
            $current_route_map = &$current_route_map["routes"][$route_part];
        }
        $current_route_map["handlers"][$method] = $callback;
    }

    function route(string $path)
    {
        if($path === "") $path = "/";

        $found = $this->find_matching_route($path);
        if($found == null){
            $response = new Response();
            $response->status(404)->response(Array("error" => "Not Found", "route" => $path));
            return $this->process_response($response);
        }
        
        $route = $found["route"];
        $parameters = $found["parameters"];
        
        if(!array_key_exists($_SERVER['REQUEST_METHOD'], $route["handlers"]))
        {
            $response = new Response();
            $response->status(405)->response(Array("error" => "Method not Allowed", "route" => $path));
            return $this->process_response($response);
        }
        
        $req = $this->mount_request($route, $parameters);
        $res = new Response();
        
        $route["handlers"][$_SERVER['REQUEST_METHOD']]($req, $res);
        
        $this->process_response($res);
    }

    private function find_matching_route(string $path)
    {
        $path_parts = explode("/", $path);
        $current_route_map = &$this->routes_map;
        $route_parameters = Array();
        foreach ($path_parts as $path_part) {
            if(array_key_exists($path_part, $current_route_map["routes"]))
                $current_route_map = &$current_route_map["routes"][$path_part];
            else{
                $found = false;
                foreach ($current_route_map["routes"] as $route => $obj_route) {
                    if(strpos($route, ":") === 0)
                    {
                        $route_parameters[substr($route, 1)] = $path_part;
                        $current_route_map = &$current_route_map["routes"][$route];
                        $found = true;
                        break;
                    }
                }
                if($found == false)
                    return null;
            }

        }
        
        return Array(
            "route" => $current_route_map, 
            "parameters" => $route_parameters
        );
    }

    private function mount_request($route, $parameters)
    {
        $req = new Request();
        $req->route = $route["route"];
        $req->method = $_SERVER['REQUEST_METHOD'];
        $req->query = $_GET;
        $req->parameters = $parameters;
        $req->body = json_decode(file_get_contents('php://input'));
        return $req;
    }

    private function process_response(Response $res)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($res->status_code);
        echo json_encode($res->response);
    }
}
