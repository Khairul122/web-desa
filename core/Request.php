<?php

namespace App\Core;

class Request
{
    private $query;
    private $request;
    private $attributes = [];
    private $server;
    private $files;
    private $cookies;
    private $content;
    private $method;
    private $uri;
    private $json = null;

    public function __construct()
    {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->method = $this->server['REQUEST_METHOD'];

        $requestUri = (string) ($this->server['REQUEST_URI'] ?? '/');
        $requestPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';

        $basePath = (string) (parse_url(BASE_URL, PHP_URL_PATH) ?: '');
        if ($basePath === '/') {
            $basePath = '';
        } else {
            $basePath = rtrim($basePath, '/');
        }

        $this->uri = $requestPath;
        if ($basePath !== '' && strpos($requestPath, $basePath) === 0) {
            $this->uri = substr($requestPath, strlen($basePath));
        }

        if ($this->uri === '' || $this->uri[0] !== '/') {
            $this->uri = '/' . ltrim($this->uri, '/');
        }

        $this->uri = parse_url($this->uri, PHP_URL_PATH) ?: '/';
        if ($this->uri !== '/') {
            $this->uri = rtrim($this->uri, '/');
            if ($this->uri === '') {
                $this->uri = '/';
            }
        }
        $this->content = file_get_contents('php://input');
    }

    public static function capture(): self
    {
        return new self();
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri ?? '/';
    }

    public function getPath(): string
    {
        return trim(parse_url($this->uri, PHP_URL_PATH), '/');
    }

    public function getQuery(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function get(string $key = null, $default = null)
    {
        return $this->getQuery($key, $default);
    }

    public function getRequest(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->request;
        }
        return $this->request[$key] ?? $default;
    }

    public function post(string $key = null, $default = null)
    {
        return $this->getRequest($key, $default);
    }

    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }

    public function only(array $keys): array
    {
        $data = $this->all();
        return array_filter($data, fn($key) => in_array($key, $keys), ARRAY_FILTER_USE_KEY);
    }

    public function except(array $keys): array
    {
        $data = $this->all();
        return array_filter($data, fn($key) => !in_array($key, $keys), ARRAY_FILTER_USE_KEY);
    }

    public function has(string $key): bool
    {
        return isset($this->query[$key]) || isset($this->request[$key]);
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function file(string $key)
    {
        return $this->files[$key] ?? null;
    }

    public function getServer(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->server;
        }
        return $this->server[$key] ?? $default;
    }

    public function getHeader(string $key = null, $default = null)
    {
        $key = strtoupper(str_replace('-', '_', $key));
        $key = 'HTTP_' . $key;
        return $this->server[$key] ?? $default;
    }

    public function getCookie(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->cookies;
        }
        return $this->cookies[$key] ?? $default;
    }

    public function setParam(string $key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getParam(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    public function setParams(array $params): self
    {
        $this->attributes = array_merge($this->attributes, $params);
        return $this;
    }

    public function getParams(): array
    {
        return $this->attributes;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function json(string $key = null, $default = null)
    {
        if ($this->json === null) {
            $this->json = json_decode($this->content, true) ?? [];
        }
        
        if ($key === null) {
            return $this->json;
        }
        
        return $this->json[$key] ?? $default;
    }

    public function isAjax(): bool
    {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }

    public function isSecure(): bool
    {
        return $this->getServer('HTTPS') === 'on' || $this->getServer('SERVER_PORT') === 443;
    }

    public function getIp(): string
    {
        return $this->getServer('REMOTE_ADDR') ?? '0.0.0.0';
    }

    public function getUserAgent(): string
    {
        return $this->getServer('HTTP_USER_AGENT') ?? '';
    }

    public function hasFileUpload(): bool
    {
        return !empty($this->files);
    }
}
