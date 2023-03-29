<?php

class AccessDeniedException extends BaseException
{
    #[\JetBrains\PhpStorm\Pure] public function __construct(string $message = "Access denied", int $code = 403, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}