<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePostTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('posts', [
            'id' => false,
            'primary_key' => ['id'],
        ]);

        $table
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('author_id', 'string', ['limit' => 36, 'null' => false])
            ->addColumn('book_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('rating', 'decimal', ['precision' => 2, 'scale' => 1])
            ->addColumn('summary', 'text')
            ->addColumn('content', 'text', ['null' => false])
            ->addColumn('status', 'enum', ['values' => ['draft', 'published'], 'default' => 'draft', 'null' => false])
            ->addColumn('view_count', 'integer', ['signed' => false, 'default' => 0, 'null' => false])
            ->addColumn('published_at', 'datetime')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'datetime')
            ->addIndex(['slug'], ['unique' => true])
            ->addForeignKey('author_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
            ])
            ->addForeignKey('book_id', 'books', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
            ])
            ->create();
    }
}
