<?php

namespace App\Core;

require_once CONFIG_PATH . '/app.php';

class Database
{
    private static $instance = null;
    private $pdo;
    private $transactionCount = 0;

    private function __construct()
    {
        $config = require CONFIG_PATH . '/database.php';

        $hosts = [];
        if (!empty($config['host'])) {
            $hosts[] = (string) $config['host'];
        }
        if (!empty($config['hosts']) && is_array($config['hosts'])) {
            foreach ($config['hosts'] as $candidateHost) {
                if (!is_string($candidateHost)) {
                    continue;
                }
                $candidateHost = trim($candidateHost);
                if ($candidateHost !== '' && !in_array($candidateHost, $hosts, true)) {
                    $hosts[] = $candidateHost;
                }
            }
        }

        if ($hosts === []) {
            throw new \Exception('Database configuration invalid: no database host provided.');
        }

        $lastError = null;
        foreach ($hosts as $host) {
            $dsn = sprintf(
                '%s:host=%s;dbname=%s;charset=%s',
                $config['driver'],
                $host,
                $config['database'],
                $config['charset']
            );

            try {
                $this->pdo = new \PDO($dsn, $config['username'], $config['password'], $config['options']);
                return;
            } catch (\PDOException $e) {
                $lastError = $e->getMessage();
            }
        }

        throw new \Exception('Database connection failed: ' . (string) $lastError);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        return $this->pdo;
    }

    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this->pdo, $table);
    }

    public function beginTransaction(): bool
    {
        if (!$this->pdo->inTransaction()) {
            return $this->pdo->beginTransaction();
        }
        $this->transactionCount++;
        return true;
    }

    public function commit(): bool
    {
        if ($this->transactionCount > 0) {
            $this->transactionCount--;
            return true;
        }
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        if ($this->transactionCount > 0) {
            $this->transactionCount--;
            return true;
        }
        return $this->pdo->rollBack();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}

class QueryBuilder
{
    private $pdo;
    private $table;
    private $selects = ['*'];
    private $wheres = [];
    private $bindings = [];
    private $orders = [];
    private $limit;
    private $offset;
    private $joins = [];
    private $groupBy;
    private $havings;

    public function __construct(\PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function select(array $columns = ['*']): self
    {
        $this->selects = $columns;
        return $this;
    }

    public function where(string $column, $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $placeholder = ':where_' . count($this->bindings);
        $this->wheres[] = "$column $operator $placeholder";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    public function orWhere(string $column, $operator, $value = null): self
    {
        $type = (count($this->wheres) === 0) ? 'WHERE' : 'OR';
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $placeholder = ':orwhere_' . count($this->bindings);
        $this->wheres[] = "$type $column $operator $placeholder";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = ":wherein_$i";
            $placeholders[] = $placeholder;
            $this->bindings[$placeholder] = $value;
        }
        $this->wheres[] = "$column IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }

    public function whereNull(string $column): self
    {
        $type = (count($this->wheres) === 0) ? 'WHERE' : 'AND';
        $this->wheres[] = "$type $column IS NULL";
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $type = (count($this->wheres) === 0) ? 'WHERE' : 'AND';
        $this->wheres[] = "$type $column IS NOT NULL";
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "INNER JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = "$column $direction";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function groupBy(string $column): self
    {
        $this->groupBy = $column;
        return $this;
    }

    public function count(string $column = '*'): int
    {
        $sql = $this->buildSelect("COUNT($column) as total");
        $stmt = $this->pdo->prepare($sql);
        if ($stmt === false) {
            return 0;
        }
        $this->bindValues($stmt);
        $stmt->execute();
        return (int) $stmt->fetch()['total'];
    }

    public function exists(): bool
    {
        $result = $this->first();
        return $result !== null;
    }

    public function first()
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function get(): array
    {
        $sql = $this->buildSelect('*');
        $stmt = $this->pdo->prepare($sql);
        if ($stmt === false) {
            return [];
        }
        $this->bindValues($stmt);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function paginate(int $perPage = 10, int $page = 1): array
    {
        $total = $this->count();
        $lastPage = (int) ceil($total / $perPage);
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;

        $this->limit($perPage)->offset($offset);

        return [
            'data' => $this->get(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    public function insert(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return (int) $this->pdo->lastInsertId();
    }

    public function update(array $data): bool
    {
        $sets = [];
        foreach ($data as $column => $value) {
            $sets[] = "$column = :$column";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        if ($stmt === false) {
            return false;
        }
        $this->bindValues($stmt);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        if ($stmt === false) {
            return false;
        }
        $this->bindValues($stmt);
        
        return $stmt->execute();
    }

    private function buildSelect(string $selectFields): string
    {
        $sql = "SELECT $selectFields FROM {$this->table}";
        
        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        
        if ($this->groupBy) {
            $sql .= " GROUP BY {$this->groupBy}";
        }
        
        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }
        
        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        return $sql;
    }

    private function bindValues(\PDOStatement $stmt): void
    {
        foreach ($this->bindings as $placeholder => $value) {
            $stmt->bindValue($placeholder, $value);
        }
    }
}
