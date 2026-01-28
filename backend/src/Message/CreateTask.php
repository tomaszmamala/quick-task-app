<?php

namespace App\Message;

use App\Enum\TaskPriority;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTask
{
    public function __construct(
        #[Assert\NotBlank(message: "Title cannot be blank.")]
        #[Assert\Length(
            min: 3, 
            max: 255, 
            minMessage: "Title must be at least 3 characters long."
        )]
        public readonly string $title,

        #[Assert\Type('string')]
        public readonly ?string $description = null,

        #[Assert\NotNull(message: "Priority is required.")]
        #[Assert\Choice(callback: [TaskPriority::class, 'values'], message: "Invalid priority value.")]
        public readonly int $priority = 1
    ) {
    }
}
