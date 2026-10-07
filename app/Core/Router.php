<?php
namespace App\Core;

/**
 * مسیریاب ساده و سریع با پشتیبانی از پارامترهای داینامیک {id}
 */
class Router
{
    private array $routes = [];
    private $notFound = null;

    public function get(string $pattern, $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, $handler, array $middleware = []): void
    {
        $this->add('POST', $pattern, $handler, $middleware);
    }

    public function any(string $pattern, $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
        $this->add('POST', $pattern, $handler, $middleware);
    }

    public function add(string $method, string $pattern, $handler, array $middleware = []): void
    {
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . rtrim($regex, '/') . '/?$#u';
        $this->routes[] = compact('method', 'pattern', 'regex', 'handler', 'middleware');
    }

    public function notFound(callable $handler): void
    {
        $this->notFound = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $isHead = ($method === 'HEAD');
        if ($isHead) {
            $method = 'GET';
        }

        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);
        if ($path !== '/' ) {
            $path = rtrim($path, '/') ?: '/';
        }


        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($route['middleware'] as $mw) {
                    Middleware::handle($mw);
                }

                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    $controller = new $class();
                    echo $controller->$action(...array_values($params));
                } else {
                    echo $handler(...array_values($params));
                }
                return;
            }
        }

        if ($this->notFound) {
            ($this->notFound)();
        } else {
            http_response_code(404);
            echo 'صفحه پیدا نشد';
        }
    }
}
