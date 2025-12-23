<?php

namespace SimplePhpRouter;

use ReflectionClass;
use ReflectionMethod;
use ReflectionAttribute;
use SimplePhpRouter\Request;
use SimplePhpRouter\Response;
use SimplePhpRouter\Attributes\Route;

final class Router
{
    /**
     * @var array<array<string, mixed>> $routes
     */
    private array $routes = [];

    /**
     * Load controllers and their routes
     *
     * @param array<string> $controllers
     * @return void
     */
    public function loadControllers(array $controllers): void
    {
        /**
         * @var object $controller
         */
        foreach ($controllers as $controller) {
            $ref = new ReflectionClass($controller);

            foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);

                foreach ($attributes as $attribute) {
                    $routeAttr      = $attribute->newInstance();
                    $this->routes[] = [
                        'path'       => $routeAttr->path,
                        'method'     => $routeAttr->method,
                        'controller' => $controller,
                        'handler'    => $method->getName(),
                    ];
                }
            }
        }
    }

    /**
     * Dispatch routes based on the current request
     *
     * @return void
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $pattern    = preg_replace('#:[a-zA-Z0-9_]+#', '([^/]+)', $route['path']);
            $finalRegex = "#^$pattern$#";

            if ($requestMethod === $route['method'] && preg_match($finalRegex, (string) $requestUri, $matches)) {
                preg_match_all('#:([a-zA-Z0-9_]+)#', $route['path'], $paramNames);
                $paramNames = $paramNames[1];

                array_shift($matches);
                $params = [];
                if (\count($paramNames) === \count($matches)) {
                    $params = array_combine($paramNames, $matches);
                }

                $controllerInstance = new $route['controller']();
                $request            = new Request($params);
                $response           = new Response();

                $controllerInstance->{$route['handler']}($request, $response);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Página no encontrada";
    }
}
