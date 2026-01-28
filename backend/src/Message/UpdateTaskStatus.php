<?php

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateTaskStatus
{
    public function __construct(
        public readonly int $id,

        #[Assert\NotNull]
        #[Assert\Type('bool')]
        public readonly bool $newStatus
    ) {}
}
