<?php

namespace App\Core;

use App\Core\Database;

class Validator
{
    private $data;
    private $rules;
    private $errors = [];
    private $validated = [];
    private $ruleMessages = [];

    public function __construct(array $data, array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    public static function check(array $data, array $rules): array
    {
        $validator = new self($data, $rules);
        return $validator->validate();
    }

    public function validate(): array
    {
        $this->errors = [];
        $this->validated = $this->data;

        foreach ($this->rules as $field => $ruleString) {
            $rules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            
            foreach ($rules as $rule) {
                $this->validateField($field, $rule);
            }
        }

        if (!empty($this->errors)) {
            return $this->errors;
        }

        return $this->validated;
    }

    public function validateField(string $field, string $rule): void
    {
        $value = $this->getValue($field);
        [$ruleName, $parameter] = $this->parseRule($rule);

        if ($this->isRequired($ruleName) || !$this->isEmpty($value)) {
            $method = 'validate' . ucfirst($ruleName);
            if (method_exists($this, $method)) {
                $valid = $this->$method($value, $parameter, $field);
                if (!$valid) {
                    $this->addError($field, $ruleName, $parameter);
                }
            }
        }
    }

    private function parseRule(string $rule): array
    {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $parameter] = explode(':', $rule, 2);
            return [$ruleName, $parameter];
        }
        return [$rule, null];
    }

    private function getValue(string $field)
    {
        return $this->data[$field] ?? null;
    }

    private function isEmpty($value): bool
    {
        return $value === null || $value === '' || (is_array($value) && empty($value));
    }

    private function isRequired(string $rule): bool
    {
        return $rule === 'required';
    }

    private function addError(string $field, string $rule, $parameter = null): void
    {
        $message = $this->getMessage($field, $rule, $parameter);
        $this->errors[$field][] = $message;
        
        if (isset($this->validated[$field])) {
            unset($this->validated[$field]);
        }
    }

    public function getMessage(string $field, string $rule, $parameter = null): string
    {
        $customMessage = $this->ruleMessages["{$field}.{$rule}"] ?? null;
        if ($customMessage) {
            return str_replace([':attribute', ':param'], [$field, $parameter], $customMessage);
        }

        $messages = [
            'required' => ':attribute wajib diisi',
            'email' => ':attribute harus berupa email yang valid',
            'min' => ':attribute minimal :param karakter',
            'max' => ':attribute maksimal :param karakter',
            'numeric' => ':attribute harus berupa angka',
            'integer' => ':attribute harus berupa bilangan bulat',
            'string' => ':attribute harus berupa teks',
            'alpha' => ':attribute hanya boleh huruf',
            'alpha_num' => ':attribute hanya boleh huruf dan angka',
            'alpha_dash' => ':attribute hanya boleh huruf, angka, dan strip',
            'url' => ':attribute harus berupa URL yang valid',
            'confirmed' => ':attribute tidak cocok dengan konfirmasi',
            'unique' => ':attribute sudah digunakan',
            'exists' => ':attribute tidak ditemukan',
            'date' => ':attribute harus berupa tanggal yang valid',
            'regex' => ':attribute tidak sesuai format',
            'image' => ':attribute harus berupa gambar',
            'mimes' => ':attribute harus berformat: :param',
        ];

        $message = $messages[$rule] ?? ':attribute tidak valid';
        $attribute = str_replace('_', ' ', $field);
        
        return str_replace([':attribute', ':param'], [$attribute, $parameter], $message);
    }

    public function setMessage(string $rule, string $message): self
    {
        $this->ruleMessages[$rule] = $message;
        return $this;
    }

    public function setCustomMessages(array $messages): self
    {
        foreach ($messages as $key => $message) {
            $this->ruleMessages[$key] = $message;
        }
        return $this;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function validated(): array
    {
        return $this->validated;
    }

    public function failed(): array
    {
        return $this->errors;
    }

    private function validateRequired($value): bool
    {
        if (is_array($value)) {
            return !empty($value);
        }
        return $value !== null && $value !== '';
    }

    private function validateEmail($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateMin($value, $parameter): bool
    {
        return strlen($value) >= (int) $parameter;
    }

    private function validateMax($value, $parameter): bool
    {
        return strlen($value) <= (int) $parameter;
    }

    private function validateNumeric($value): bool
    {
        return is_numeric($value);
    }

    private function validateInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function validateString($value): bool
    {
        return is_string($value);
    }

    private function validateAlpha($value): bool
    {
        return preg_match('/^[a-zA-Z]+$/', $value) === 1;
    }

    private function validateAlphaNum($value): bool
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $value) === 1;
    }

    private function validateAlphaDash($value): bool
    {
        return preg_match('/^[a-zA-Z0-9\-_]+$/', $value) === 1;
    }

    private function validateUrl($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function validateConfirmed($value, $parameter, string $field): bool
    {
        return $value === $this->getValue($parameter ?? $field . '_confirmation');
    }

    private function validateUnique($value, $parameter, string $field): bool
    {
        $table = $parameter;
        $column = $field;
        
        $exists = Database::getInstance()->table($table)
            ->where($column, $value)
            ->exists();
        
        return !$exists;
    }

    private function validateExists($value, $parameter, string $field): bool
    {
        $table = $parameter;
        $column = $field;
        
        return Database::getInstance()->table($table)
            ->where($column, $value)
            ->exists();
    }

    private function validateDate($value): bool
    {
        $date = date_create($value);
        return $date !== false;
    }

    private function validateRegex($value, $parameter): bool
    {
        return preg_match($parameter, $value) === 1;
    }

    private function validateImage($value): bool
    {
        if (!is_array($value) || !isset($value['tmp_name'])) {
            return false;
        }
        
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $value['tmp_name']);
        finfo_close($finfo);
        
        return in_array($mime, $allowed);
    }

    private function validateMimes($value, $parameter): bool
    {
        if (!is_array($value) || !isset($value['tmp_name'])) {
            return false;
        }
        
        $allowed = explode(',', $parameter);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $value['tmp_name']);
        finfo_close($finfo);
        
        return in_array($mime, $allowed);
    }
}
