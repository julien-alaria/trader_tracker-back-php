<?php

namespace TraderTracker\Php;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $pattern = preg_replace('#:[a-zA-Z]+#', '([^/]+)', $path);
        $this->routes[$method][] = [
            'pattern' => "#^" . $pattern . "$#",
            'handler' => $handler,
            'paramNames' => $this->extractParamNames($path),
        ];
    }

    private function extractParamNames(string $path): array
    {
        preg_match_all('#:([a-zA-Z]+)#', $path, $matches);
        return $matches[1];
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches);
                $params = array_combine($route['paramNames'], $matches);
                call_user_func($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(["message" => "Route not found"]);
    }
}