<?php

namespace App\Core;

use App\Core\Database;
use App\Core\Session;
use App\Core\Response;
use App\Core\View;
use App\Core\Validator;

abstract class Controller
{
    protected $db;
    protected $request;
    protected $response;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->request = Request::capture();
        $this->response = new Response();
    }

    protected function model(string $model): object
    {
        $modelClass = 'App\\Models\\' . $model;
        return new $modelClass();
    }

    protected function view(string $view, array $data = []): Response
    {
        return Response::view($view, $data);
    }

    protected function render(string $view, array $data = []): string
    {
        return View::make($view, $data);
    }

    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    protected function redirectBack(): Response
    {
        return Response::back();
    }

    protected function redirectWith(string $url, string $key, $value): Response
    {
        Session::flash($key, $value);
        return $this->redirect($url);
    }

    protected function with(string $key, $value): Response
    {
        return $this->response->with($key, $value);
    }

    protected function withSuccess(string $message): Response
    {
        return $this->response->withSuccess($message);
    }

    protected function withError(string $message): Response
    {
        return $this->response->withError($message);
    }

    protected function withErrors(array $errors): Response
    {
        return $this->response->withErrors($errors);
    }

    protected function json(array $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    protected function validate(array $rules): array
    {
        $validator = new Validator($this->request->all(), $rules);
        return $validator->validate();
    }

    protected function validateRequest(array $rules): array
    {
        return $this->validate($rules);
    }

    protected function abort(int $code = 404, string $message = ''): void
    {
        http_response_code($code);
        if ($message) {
            echo $message;
        } else {
            include APP_PATH . '/Views/errors/' . $code . '.php';
        }
        exit;
    }

    protected function authorize($condition, string $message = 'Unauthorized'): void
    {
        if (!$condition) {
            if ($this->request->isAjax()) {
                Response::json(['error' => $message], 403)->send();
            } else {
                $this->abort(403, $message);
            }
        }
    }
}
