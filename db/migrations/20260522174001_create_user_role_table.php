<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserRoleTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('user_roles', ['id' => false, 'primary_key' => ['user_id', 'role_id']]);

        $table
            ->addColumn('user_id', 'string', ['limit' => 36, 'null' => false])
            ->addColumn('role_id', 'integer', ['signed' => false, 'null' => false])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_user_roles_user_id'
            ])
            ->addForeignKey('role_id', 'roles', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_user_roles_role_id'
            ])
            ->create();
    }
}