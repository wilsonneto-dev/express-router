# Express Router

### 🎈 The simplest, easiest, and most minimalist way 😃 to create routes in a pure PHP app

Install it in your project:
```console
composer require wilsonneto-dev/express-router
```

Use it in your project:
```php
require_once __DIR__ . "/vendor/autoload.php";

$router = new ExpressRouter\Router();

// ... register your routes...

$router->route(isset($_GET["path"]) ? $_GET["path"] : "");
```

Simple example of creating routes
```php
require_once __DIR__ . "/vendor/autoload.php";

$router = new ExpressRouter\Router();

$router->get("/", function ($req, $res) {
    return $res->response(array("ok" => true, "tip" => "Use /articles"));
});

$router->get("/articles", function ($req, $res) {
    return $res->response(array("example" => "list of articles"));
});

$router->get("/articles/:id", function ($req, $res) {
    return $res->response(array("example" => "details of the article with id " . $req->parameters["id"]));
});

$router->get("/articles/:id/comments", function ($req, $res) {
    return $res->response(array("example" => "list of comments of the article with id " . $req->parameters["id"]));
});

$router->post("/articles", function ($req, $res) {
    return $res->response(array("example" => "create a new article"));
});

$router->put("/articles/:id", function ($req, $res) {
    return $res->response(array("example" => "update the article with id " . $req->parameters["id"]));
});

$router->route(isset($_GET["path"]) ? $_GET["path"] : "");
```

You will need the rewrite mod enabled and a `.htaccess` file as the below: 
```
<Files app.ini> 
    Order Allow,Deny
    Deny from all
</Files>

RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?path=$1 [NC,L,QSA]
```

#### Example project:

https://github.com/wilsonneto-dev/express-router-example

#### Request and Response objects

your router callbacks will receive two objects, the request with all the request information; and the response is the object responsible by prepare the response.

```php
class Request {
    // the route parameters
    public ?Array $parameters;

    // query parameters 
    public ?Array $query = null;

    // body payload
    public $body = null;

    // the request route
    public string $route = "";

    // the request method
    public string $method = "";
}
```

```
class Response {
    // use this method to control the response status code, default 200. 
    public function status(int $new_status_code);

    // use this method to return the response
    public function response(Array $response);
}
```

And yes, that's all! <br />
Life can be simple sometimes ✨
