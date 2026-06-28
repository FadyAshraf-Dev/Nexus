<?php

declare(strict_types=1);

final class CategoryRepository extends Repository
{
    public function create(array $data): int
    {
        $sql = "
        INSERT INTO categories
        (
            category_name,
            slug
        )
        VALUES
        (
            :category_name,
            :slug
        )";

        $statement = $this->prepare($sql);

        $statement->execute([
            'category_name' => $data['category_name'],
            'slug'          => $data['slug'],
        ]);

        return $this->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
        UPDATE categories
        SET
            category_name = :category_name,
            slug = :slug
        WHERE id = :id";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id'            => $id,
            'category_name' => $data['category_name'],
            'slug'          => $data['slug'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "
        DELETE FROM categories
        WHERE id = :id";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        return $statement->rowCount() > 0;
    }

    public function findById(int $id): ?array
    {
        $sql = "
        SELECT *
        FROM categories
        WHERE id = :id
        LIMIT 1";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $category = $statement->fetch();

        return $category ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $sql = "
        SELECT *
        FROM categories
        WHERE slug = :slug
        LIMIT 1";

        $statement = $this->prepare($sql);

        $statement->execute([
            'slug' => $slug
        ]);

        $category = $statement->fetch();

        return $category ?: null;
    }

    public function findAll(): array
    {
        $sql = "
        SELECT *
        FROM categories
        ORDER BY category_name ASC";

        return $this->query($sql)->fetchAll();
    }

    public function exists(int $id): bool
    {
        $sql = "
        SELECT 1
        FROM categories
        WHERE id = :id
        LIMIT 1";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        return (bool) $statement->fetchColumn();
    }
}