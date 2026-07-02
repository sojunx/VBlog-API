<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoryTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('categories', ['signed' => false]);

        $table
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['slug'], ['unique' => true])
            ->create();
    }
}
