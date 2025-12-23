<?php

namespace SimplePhpRouter;

class Request
{
    /**
     * Body of the request
     * @var array<string, string|int|float|bool|null>
     */
    public array $body    = [];

    /**
     * Files uploaded with the request
     * @var array<string, array<string>>
     */
    public array $files   = [];

    /**
     * Route parameters
     * @var array<string, string>
     */
    public array $params  = [];

    /**
     * Query parameters
     * @var array<string, string>
     */
    public array $queries = [];

    /**
     * Constructor
     * @param array<string, string> $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->getBody();
        $this->getFiles();
        $this->getQueries();
    }

    /**
     * Get the body of the request
     * @return void
     */
    private function getBody(): void
    {
        try {
            $rawBody = file_get_contents('php://input');

            if ($rawBody) {
                $parsedBody = json_decode($rawBody, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON body');
                }

                $this->body = $parsedBody;
            }
        } catch (\Throwable $th) {
            $this->body = [];
        }
    }

    /**
     * Get the files uploaded with the request
     * @return void
     */
    private function getFiles(): void
    {
        $this->files = $_FILES;
    }

    /**
     * Get the query parameters
     * @return void
     */
    private function getQueries(): void
    {
        $uri   = $_SERVER['REQUEST_URI'] ?? '';
        $parts = explode('?', $uri, 2);

        if (count($parts) < 2 || empty($parts[1])) {
            $this->queries = [];
            return;
        }

        $queryFullStr = $parts[1];
        $queryList    = explode('&', $queryFullStr);

        foreach ($queryList as $queryItem) {
            if (strpos($queryItem, '=') !== false) {
                [$queryName, $queryValue]     = explode('=', $queryItem, 2);
                $this->queries[$queryName]    = $queryValue;
            }
        }
    }
}
