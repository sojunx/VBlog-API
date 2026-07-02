<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoryBookTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('category_books', ['id' => false, 'primary_key' => ['category_id', 'book_id']]);

        $table
            ->addColumn('category_id', 'integer', ['signed' => false])
            ->addColumn('book_id', 'integer', ['signed' => false])
            ->addForeignKey('category_id', 'categories', 'id', [
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
