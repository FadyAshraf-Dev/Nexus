<?php

declare(strict_types=1);

abstract class Repository
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    protected function prepare(
        string $sql
    ): PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    protected function query(
        string $sql
    ): PDOStatement
    {
        return $this->pdo->query($sql);
    }

    protected function lastInsertId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }

    protected function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    protected function commit(): bool
    {
        return $this->pdo->commit();
    }

    protected function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    protected function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }
}