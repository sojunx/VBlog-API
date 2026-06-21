<?php

namespace App\Exceptions;

class BadRequestException extends ApiException {
    public function __construct(string $message) {
        parent::__construct($message);
    }
}