<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoryTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('categories', ['signed' => false]);

        $table
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
