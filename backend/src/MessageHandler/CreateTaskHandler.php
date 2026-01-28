<?php

namespace App\MessageHandler;

use App\Entity\Task;
use App\Enum\TaskPriority;
use App\Message\CreateTask;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateTaskHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(CreateTask $command): void
    {
        $task = new Task();
        $task->setTitle($command->title);
        $task->setDescription($command->description);
        $task->setPriority(TaskPriority::from($command->priority));

        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }
}
