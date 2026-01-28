<?php

namespace App\MessageHandler;

use App\Message\UpdateTaskStatus;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsMessageHandler]
class UpdateTaskStatusHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(UpdateTaskStatus $command): void
    {
        $task = $this->taskRepository->find($command->id);

        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        $task->setStatus($command->newStatus);
        $this->entityManager->flush();
    }
}
