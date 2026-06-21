<?php

namespace App\Exceptions;

use Fig\Http\Message\StatusCodeInterface as HTTP;
use RuntimeException;

abstract class ApiException extends RuntimeException {
    public function __construct(string $message = "", private readonly int $status_code = HTTP::STATUS_BAD_REQUEST) {
        parent::__construct($message);
    }

    public function getStatusCode(): int {
        return $this->status_code;
    }
}