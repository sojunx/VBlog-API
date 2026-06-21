<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRoleTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('roles', ['signed' => false]);

        $table
            ->addColumn('code', 'string', ['null' => false])
            ->addIndex(['code'], ['unique' => true])
            ->create();
    }
}