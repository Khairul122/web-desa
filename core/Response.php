<?php

namespace App\Core;

use App\Core\View;

class Response
{
    private $content;
    private $statusCode;
    private $headers = [];

    public function __construct($content = '', int $statusCode = 200)
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
    }

    public static function make($content = '', int $statusCode = 200): self
    {
        return new self($content, $statusCode);
    }

    public static function json(array $data, int $statusCode = 200): self
    {
        $response = new self(json_encode($data, JSON_PRETTY_PRINT), $statusCode);
        $response->header('Content-Type', 'application/json');
        return $response;
    }

    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self('', $statusCode);
        $response->header('Location', $url);
        return $response;
    }

    public static function back(int $statusCode = 302): self
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
        return self::redirect($referer, $statusCode);
    }

    public static function download(string $filePath, string $name = null): self
    {
        $name = $name ?? basename($filePath);
        $response = new self(file_get_contents($filePath), 200);
        $response->header('Content-Type', 'application/octet-stream');
        $response->header('Content-Disposition', 'attachment; filename="' . $name . '"');
        $response->header('Content-Length', filesize($filePath));
        return $response;
    }

    public static function view(string $view, array $data = [], int $statusCode = 200): self
    {
        $content = View::renderWithLayout($view, $data, 'layouts/app');
        return new self($content, $statusCode);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function getHeader(string $key)
    {
        return $this->headers[$key] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function with(string $key, $value): self
    {
        if (is_array($value)) {
            $_SESSION['_flash'][$key] = serialize($value);
        } else {
            $_SESSION['_flash'][$key] = $value;
        }
        return $this;
    }

    public function withSuccess(string $message): self
    {
        return $this->with('success', $message);
    }

    public function withError(string $message): self
    {
        return $this->with('error', $message);
    }

    public function withErrors(array $errors): self
    {
        return $this->with('errors', $errors);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo $this->content;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
