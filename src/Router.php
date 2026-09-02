<?php

namespace TraderTracker\Php;

class Router {

    private array $routes = [];

    public function get(string $path, callable|array ...$handlers): void {
        $this->addRoute('GET', $path, $handlers);
    }

    public function post(string $path, callable|array ...$handlers): void {
        $this->addRoute('POST', $path, $handlers);
    }

    public function put(string $path, callable|array ...$handlers): void {
        $this->addRoute('PUT', $path, $handlers);
    }

    public function delete(string $path, callable|array ...$handlers): void {
        $this->addRoute('DELETE', $path, $handlers);
    }

    private function addRoute(string $method, string $path, array $handlers): void {
        $pattern = preg_replace('#:[a-zA-Z]+#', '([^/]+)', $path);
        $this->routes[$method][] = [
            'pattern' => "#^" . $pattern . "$#",
            'handlers' => $handlers,
            'paramNames' => $this->extractParamNames($path),
        ];
    }

    private function extractParamNames(string $path): array {
        preg_match_all('#:([a-zA-Z]+)#', $path, $matches);
        return $matches[1];
    }

    public function dispatch(string $method, string $path): void {
        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches);
                $params = array_combine($route['paramNames'], $matches);

                try {
                    foreach($route['handlers'] as $handler) {
                        $result = call_user_func($handler, $params);
                        if (is_array($result)) {
                            $params = array_merge($params, $result);
                        }
                    }
                } catch (\Throwable $e) {
                \TraderTracker\Php\Utils\ErrorHandler::respond($e);
                } 
                return;
            }
        }

        http_response_code(404);
        echo json_encode(["message" => "Route not found"]);
    }
}