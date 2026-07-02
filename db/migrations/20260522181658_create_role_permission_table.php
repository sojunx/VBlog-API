<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRolePermissionTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('role_permissions', ['id' => false, 'primary_key' => ['role_id', 'permission_id']]);

        $table
            ->addColumn('role_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('permission_id', 'integer', ['signed' => false, 'null' => false])
            ->addForeignKey('role_id', 'roles', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
            ])
            ->addForeignKey('permission_id', 'permissions', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
            ])
            ->create();
    }
}