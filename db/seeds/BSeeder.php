<?php

use Phinx\Seed\AbstractSeed;

class BSeeder extends AbstractSeed {
    protected function seed(string $table, array $data): void {
        $table = $this->table($table);

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $table->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $table->insert($data)->saveData();
    }
}