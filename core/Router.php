<?php

namespace App\Core;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Throwable;

class Router
{
    private $routes = [];
    private $currentRoute = null;
    private $middlewareGroups = [];
    private $routePatterns = [
        '{id}' => '([0-9]+)',
        '{slug}' => '([a-z0-9\-]+)',
        '{any}' => '(.+)',
        '{num}' => '([0-9]+)',
        '{alpha}' => '([a-zA-Z]+)',
    ];

    public function get(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function patch(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('PATCH', $path, $handler, $middleware);
    }

    public function group(array $attributes, callable $callback): self
    {
        $previousGroup = $this->middlewareGroups;
        $this->middlewareGroups = array_merge($previousGroup, $attributes);
        $callback($this);
        $this->middlewareGroups = $previousGroup;
        return $this;
    }

    private function addRoute(string $method, string $path, $handler, array $middleware): self
    {
        $pattern = $this->convertPathToRegex($path);
        $middleware = array_merge($this->middlewareGroups['middleware'] ?? [], $middleware);
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
            'name' => $this->generateRouteName($path, $handler)
        ];
        
        return $this;
    }

    private function convertPathToRegex(string $path): string
    {
        $pattern = preg_quote($path, '/');
        $pattern = str_replace('\\{id\\}', '([0-9]+)', $pattern);
        $pattern = str_replace('\\{slug\\}', '([a-z0-9\-]+)', $pattern);
        $pattern = str_replace('\\{any\\}', '(.+)', $pattern);
        $pattern = str_replace('\\{num\\}', '([0-9]+)', $pattern);
        $pattern = str_replace('\\{alpha\\}', '([a-zA-Z]+)', $pattern);
        return '#^' . $pattern . '$#';
    }

    private function generateRouteName(string $path, $handler): string
    {
        if (is_array($handler)) {
            return strtolower($handler[0]) . '.' . strtolower($handler[1]);
        }
        return str_replace(['/', '{', '}'], ['.', '', ''], $path);
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $this->currentRoute = $route;
                $params = $this->extractParams($route['path'], $matches);
                $request->setParams($params);

                return $this->handleRoute($route, $request);
            }
        }

        return $this->handleNotFound();
    }

    private function extractParams(string $path, array $matches): array
    {
        $params = [];
        preg_match_all('/\{(\w+)\}/', $path, $paramNames);
        
        array_shift($matches);
        
        foreach ($paramNames[1] as $index => $name) {
            $params[$name] = $matches[$index] ?? null;
        }
        
        return $params;
    }

    private function handleRoute(array $route, Request $request): Response
    {
        $handler = $route['handler'];
        
        try {
            $this->runMiddleware($route['middleware'], $request);
            
            if (is_callable($handler)) {
                $response = $this->invokeCallable($handler, $request);
            } elseif (is_array($handler)) {
                [$controller, $method] = $handler;
                $controllerInstance = new $controller();
                $response = $this->invokeControllerMethod($controllerInstance, $method, $request);
            } else {
                throw new Exception("Invalid route handler");
            }
            
            if ($response instanceof Response) {
                return $response;
            }
            
            return new Response($response, 200);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    private function invokeCallable(callable $handler, Request $request)
    {
        if ($handler instanceof Closure || is_string($handler)) {
            $reflection = new ReflectionFunction($handler);
        } else {
            $reflection = new ReflectionFunction(Closure::fromCallable($handler));
        }
        $args = $this->buildInvocationArgs($reflection->getParameters(), $request);
        return call_user_func_array($handler, $args);
    }

    private function invokeControllerMethod(object $controllerInstance, string $method, Request $request)
    {
        $reflection = new ReflectionMethod($controllerInstance, $method);
        $args = $this->buildInvocationArgs($reflection->getParameters(), $request);
        return $reflection->invokeArgs($controllerInstance, $args);
    }

    private function buildInvocationArgs(array $parameters, Request $request): array
    {
        $routeParams = $request->getParams();
        $orderedRouteValues = array_values($routeParams);
        $orderedIndex = 0;
        $args = [];

        foreach ($parameters as $parameter) {
            if ($this->expectsRequest($parameter)) {
                $args[] = $request;
                continue;
            }

            $name = $parameter->getName();
            if (array_key_exists($name, $routeParams)) {
                $args[] = $routeParams[$name];
                continue;
            }

            while ($orderedIndex < count($orderedRouteValues)) {
                $value = $orderedRouteValues[$orderedIndex++];
                $args[] = $value;
                continue 2;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
                continue;
            }

            if ($parameter->allowsNull()) {
                $args[] = null;
            }
        }

        return $args;
    }

    private function expectsRequest(ReflectionParameter $parameter): bool
    {
        $type = $parameter->getType();
        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return $parameter->getName() === 'request';
        }

        return $type->getName() === Request::class || is_subclass_of($type->getName(), Request::class);
    }

    private function runMiddleware(array $middleware, Request $request): void
    {
        foreach ($middleware as $middlewareClass) {
            if (class_exists($middlewareClass)) {
                $instance = new $middlewareClass();
                if ($instance instanceof Middleware) {
                    $instance->handle($request);
                }
            }
        }
    }

    private function handleNotFound(): Response
    {
        http_response_code(404);
        return new Response($this->renderErrorPage(404), 404);
    }

    private function handleException(Throwable $e): Response
    {
        $requestMethod = (string) ($_SERVER['REQUEST_METHOD'] ?? 'CLI');
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $errorMessage = sprintf(
            "[%s] Unhandled exception (%s): %s in %s:%d | %s %s\n%s",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            (int) $e->getLine(),
            $requestMethod,
            $requestUri,
            $e->getTraceAsString()
        );
        error_log($errorMessage);

        http_response_code(500);
        return new Response($this->renderErrorPage(500, $e), 500);
    }

    private function renderErrorPage(int $code, Throwable $e = null): string
    {
        ob_start();
        http_response_code($code);
        include APP_PATH . '/Views/errors/' . $code . '.php';
        if (ENVIRONMENT === 'development' && $e) {
            echo '<pre>' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
        }
        return ob_get_clean();
    }

    public function url(string $name, array $params = []): string
    {
        foreach ($this->routes as $route) {
            if ($route['name'] === $name) {
                $url = $route['path'];
                foreach ($params as $key => $value) {
                    $url = str_replace('{' . $key . '}', $value, $url);
                }
                return BASE_URL . $url;
            }
        }
        return BASE_URL;
    }

    public function getCurrentRoute(): ?array
    {
        return $this->currentRoute;
    }
}
