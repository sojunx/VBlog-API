<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBookTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('books', ['signed' => false]);

        $table
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('author', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('isbn', 'string', ['limit' => 255])
            ->addColumn('cover_image_url', 'string', ['limit' => 255])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['isbn'], ['unique' => true])
            ->create();
    }
}
