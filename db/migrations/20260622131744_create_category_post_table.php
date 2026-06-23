<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoryPostTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('category_posts', ['id' => false, 'primary_key' => ['category_id', 'post_id']]);

        $table
            ->addColumn('category_id', 'integer', ['signed' => false])
            ->addColumn('post_id', 'integer', ['signed' => false])
            ->addForeignKey('category_id', 'categories', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_category_posts_category_id'
            ])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_category_posts_post_id'
            ])
            ->create();
    }
}
