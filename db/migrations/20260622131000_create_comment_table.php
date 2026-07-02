<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCommentTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('comments', ['signed' => false]);
        $table
            ->addColumn('post_id', 'biginteger', ['signed' => false, 'null' => false])
            ->addColumn('user_id', 'string', ['limit' => 36, 'null' => false]) // TODO: maybe error if user_id is null
            ->addColumn('content', 'text', ['null' => true])
            ->addColumn('rating', 'integer', ['null' => false]) // Post rating, 1 to 5 stars
            ->addColumn('status', 'string', ['limit' => 50, 'default' => 'published', 'null' => false])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_comments_post_id'
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_comments_user_id'
            ])
            ->create();
    }
}
