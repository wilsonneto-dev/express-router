# Express Router

🎈 The simplest, easiest, and most minimalist way 😃 to create routes in a pure PHP app

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

You will need the rewrite mod enabled and a htaccess as the below: 
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

And yes, that's all! <br />
Life can be simple sometimes ✨
