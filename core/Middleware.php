<?php

namespace App\Core;

use App\Core\Session;
use App\Core\Response;
use App\Core\CSRF;
use App\Core\Router;

abstract class Middleware
{
    abstract public function handle(Request $request): void;

    protected function next(Request $request): Response
    {
        return (new Router())->dispatch($request);
    }

    protected function respondWithError(string $message, int $statusCode = 403): Response
    {
        return Response::make($message, $statusCode);
    }

    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    protected function redirectBack(): Response
    {
        return Response::back();
    }
}

class AuthMiddleware extends Middleware
{
    public function handle(Request $request): void
    {
        if (!Session::has('user_id')) {
            Session::set('redirect_url', $request->getUri());
            $this->redirect(BASE_URL . '/login')->send();
            exit;
        }
    }
}

class GuestMiddleware extends Middleware
{
    public function handle(Request $request): void
    {
        if (Session::has('user_id')) {
            $this->redirect(BASE_URL)->send();
            exit;
        }
    }
}

class AdminMiddleware extends Middleware
{
    public function handle(Request $request): void
    {
        if (!Session::has('user_id') || Session::get('user_role') !== 'admin') {
            $this->redirect(BASE_URL)->send();
            exit;
        }
    }
}

class CSRFCheckMiddleware extends Middleware
{
    public function handle(Request $request): void
    {
        if ($request->getMethod() === 'POST' || $request->getMethod() === 'PUT' || $request->getMethod() === 'DELETE') {
            if (!CSRF::validateRequest()) {
                Response::make('Invalid CSRF token', 419)->send();
                exit;
            }
        }
    }
}

class ThrottleMiddleware extends Middleware
{
    private $maxAttempts;
    private $decayMinutes;
    private $prefix = 'throttle_';

    public function __construct(int $maxAttempts = 60, int $decayMinutes = 1)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    public function handle(Request $request): void
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->maxAttempts;
        
        if ($this->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->getRetryAfter($key);
            Response::make('Too many attempts. Please try again in ' . $retryAfter . ' seconds.', 429)
                ->header('Retry-After', $retryAfter)
                ->send();
            exit;
        }
        
        $this->hit($key);
    }

    private function resolveRequestSignature(Request $request): string
    {
        return $this->prefix . sha1($request->getIp() . '|' . $request->getUri());
    }

    private function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        $attempts = Session::get($key, 0);
        return $attempts >= $maxAttempts;
    }

    private function hit(string $key): void
    {
        $attempts = Session::get($key, 0) + 1;
        Session::set($key, $attempts);
    }

    private function getRetryAfter(string $key): int
    {
        return $this->decayMinutes * 60;
    }

    private function clear(string $key): void
    {
        Session::forget($key);
    }
}
