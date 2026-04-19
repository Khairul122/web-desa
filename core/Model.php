<?php

namespace App\Core;

abstract class Model
{
    protected static $table;
    protected static $primaryKey = 'id';
    protected static $fillable = [];
    protected static $guarded = ['id'];
    protected $db;
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->db = Database::getInstance();
        $this->fill($attributes);
    }

    public static function table(): string
    {
        return static::$table ?? strtolower(basename(str_replace('\\', '/', static::class))) . 's';
    }

    public static function all(): array
    {
        $results = Database::getInstance()->table(static::table())->get();
        return array_map(fn($row) => new static($row), $results);
    }

    public static function find(int $id): ?static
    {
        $result = Database::getInstance()->table(static::table())->where(static::$primaryKey, $id)->first();
        return $result ? new static($result) : null;
    }

    public static function findOrFail(int $id): static
    {
        $model = static::find($id);
        if (!$model) {
            abort(404);
        }
        return $model;
    }

    public static function first(): ?static
    {
        $result = Database::getInstance()->table(static::table())->first();
        return $result ? new static($result) : null;
    }

    public static function where(string $column, $value): QueryBuilder
    {
        return Database::getInstance()->table(static::table())->where($column, $value);
    }

    public static function create(array $data): static
    {
        $instance = new static();
        $id = $instance->db->table(static::table())->insert($instance->fillable($data));
        return $instance->find($id);
    }

    public function fill(array $data): self
    {
        foreach ($data as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    protected function fillable(array $data): array
    {
        if (empty(static::$fillable)) {
            return array_diff_key($data, array_flip(static::$guarded));
        }
        return array_intersect_key($data, array_flip(static::$fillable));
    }

    protected function isFillable(string $key): bool
    {
        if (in_array($key, static::$guarded)) {
            return false;
        }
        if (empty(static::$fillable)) {
            return true;
        }
        return in_array($key, static::$fillable);
    }

    public function save(): bool
    {
        $id = $this->getKey();
        
        if ($id) {
            return $this->db->table(static::table())->where(static::$primaryKey, $id)->update($this->attributes);
        }
        
        $id = $this->db->table(static::table())->insert($this->attributes);
        $this->attributes[static::$primaryKey] = $id;
        return (bool) $id;
    }

    public function delete(): bool
    {
        $id = $this->getKey();
        if (!$id) {
            return false;
        }
        return $this->db->table(static::table())->where(static::$primaryKey, $id)->delete();
    }

    public function getKey()
    {
        return $this->attributes[static::$primaryKey] ?? null;
    }

    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, $value): void
    {
        if ($this->isFillable($key)) {
            $this->attributes[$key] = $value;
        }
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function json(): string
    {
        return json_encode($this->toArray());
    }
}
