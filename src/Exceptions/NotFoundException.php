<?php

namespace App\Exceptions;

use Fig\Http\Message\StatusCodeInterface as HTTP;

class NotFoundException extends ApiException {
    public function __construct(string $message) {
        parent::__construct($message, HTTP::STATUS_NOT_FOUND);
    }
}