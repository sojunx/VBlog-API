<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePermissionTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('permissions', ['signed' => false]);

        $table
            ->addColumn('code', 'string', ['null' => false])
            ->addIndex(['code'], ['unique' => true])
            ->create();
    }
}