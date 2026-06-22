<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSessionTable extends AbstractMigration {
    public function change(): void {
        $table = $this->table('sessions');

        $table
            ->addColumn('user_id', 'string', ['limit' => 36, 'null' => false])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
                'constraint' => 'fk_sessions_user_id'
            ])
            ->addColumn('access_token_hash', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('refresh_token_hash', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('access_expires_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('refresh_expires_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['access_token_hash'], ['unique' => true])
            ->addIndex(['refresh_token_hash'], ['unique' => true])
            ->addIndex(['user_id'])
            ->create();
    }
}
