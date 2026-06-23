<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePostTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('posts', ['signed' => false]);

        $table
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('content', 'text', ['null' => false])
//            ->addColumn('status', 'enum', ['values' => ['draft', 'published'], 'default' => 'draft', 'null' => false])
//            ->addColumn('published_at', 'datetime', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('author_id', 'string', ['limit' => 36, 'null' => false])
            ->addForeignKey('author_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_posts_author_id'
            ])
            ->create();
    }
}
