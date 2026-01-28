<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeStatusInput
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type('bool')]
        public readonly bool $status
    ) {}
}
