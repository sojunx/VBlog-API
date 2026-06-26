<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddBookUpdatePermission extends AbstractMigration {
    public function up(): void {
        // 1. Insert 'book.update' permission
        $this->execute("INSERT INTO permissions (code) VALUES ('book.update')");

        // 2. Fetch last inserted ID or query the ID of 'book.update'
        $db = $this->getAdapter()->getConnection();
        $permissionId = $db->lastInsertId();

        if (!$permissionId) {
            $row = $this->fetchRow("SELECT id FROM permissions WHERE code = 'book.update'");
            if ($row) {
                $permissionId = $row['id'];
            }
        }

        // 3. Map permission 'book.update' to Admin role (role_id = 1)
        if ($permissionId) {
            $this->execute("INSERT INTO role_permissions (role_id, permission_id) VALUES (1, $permissionId)");
        }
    }

    public function down(): void {
        // Rollback mapping and delete permission
        $this->execute("DELETE FROM role_permissions WHERE permission_id = (SELECT id FROM permissions WHERE code = 'book.update')");
        $this->execute("DELETE FROM permissions WHERE code = 'book.update'");
    }
}

