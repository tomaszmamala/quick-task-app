<?php

namespace App\Message;

class UpdateTaskStatus
{
    public function __construct(
        public readonly int $id,
        public readonly bool $newStatus
    ) {}
}
