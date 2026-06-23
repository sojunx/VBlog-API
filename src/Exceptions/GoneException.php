<?php

namespace App\Exceptions;

use Fig\Http\Message\StatusCodeInterface as HTTP;

class GoneException extends ApiException {
    public function __construct(string $message) {
        parent::__construct($message, HTTP::STATUS_GONE);
    }
}
