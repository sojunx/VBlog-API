<?php

namespace App\Repositories;

use PDO;

abstract class AbstractRepository {
    public function __construct(protected PDO $db) {}
}