<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('users', ['id' => false, 'primary_key' => 'id']);

        $table
            ->addColumn('id', 'string', ['limit' => 36, 'null' => false,])
            ->addColumn('user_name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('password_hash', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('avatar_url', 'string', ['limit' => 255])
            ->addColumn('avatar_public_id', 'string', ['limit' => 255])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['email'], ['unique' => true])
            ->addIndex(['user_name'], ['unique' => true])
            ->create();
    }
}