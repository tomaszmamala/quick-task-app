<?php

namespace App\Controller;

use App\DTO\ChangeStatusInput;
use App\Message\CreateTask;
use App\Message\UpdateTaskStatus;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(TaskRepository $taskRepository): JsonResponse
    {
        $tasks = $taskRepository->findBy([], ['priority' => 'DESC']);

        return $this->json($tasks);
    }

    #[Route('', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateTask $command,
        MessageBusInterface $bus
    ): JsonResponse {
        $bus->dispatch($command);

        return $this->json(['status' => 'Task created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function updateStatus(
        int $id,
        #[MapRequestPayload] ChangeStatusInput $input,
        MessageBusInterface $bus
    ): JsonResponse {
        $bus->dispatch(new UpdateTaskStatus(
            id: $id,
            newStatus: $input->status
        ));

        return $this->json(['status' => 'Task status updated']);
    }
}
