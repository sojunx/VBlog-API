<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddBookDeletePermission extends AbstractMigration {
    public function up(): void {
        // 1. Insert 'book.delete' permission
        $this->execute("INSERT INTO permissions (code) VALUES ('book.delete')");

        // 2. Fetch last inserted ID or query the ID of 'book.delete'
        $db = $this->getAdapter()->getConnection();
        $permissionId = $db->lastInsertId();

        if (!$permissionId) {
            $row = $this->fetchRow("SELECT id FROM permissions WHERE code = 'book.delete'");
            if ($row) {
                $permissionId = $row['id'];
            }
        }

        // 3. Map permission 'book.delete' to Admin role (role_id = 1)
        if ($permissionId) {
            $this->execute("INSERT INTO role_permissions (role_id, permission_id) VALUES (1, $permissionId)");
        }
    }

    public function down(): void {
        // Rollback mapping and delete permission
        $this->execute("DELETE FROM role_permissions WHERE permission_id = (SELECT id FROM permissions WHERE code = 'book.delete')");
        $this->execute("DELETE FROM permissions WHERE code = 'book.delete'");
    }
}

