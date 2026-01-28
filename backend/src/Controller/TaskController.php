<?php

namespace App\Controller;

use App\Message\CreateTask;
use App\Message\UpdateTaskStatus;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        Request $request,
        MessageBusInterface $bus,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $command = new CreateTask(
            title: $data['title'] ?? '',
            description: $data['description'] ?? null,
            priority: $data['priority'] ?? 1
        );

        $errors = $validator->validate($command);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $bus->dispatch($command);

        return $this->json(['status' => 'Task created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function updateStatus(
        int $id,
        Request $request,
        MessageBusInterface $bus
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['status'])) {
            return $this->json(['error' => 'Field "status" (boolean) is required'], Response::HTTP_BAD_REQUEST);
        }

        $bus->dispatch(new UpdateTaskStatus(
            id: $id,
            newStatus: (bool) $data['status']
        ));

        return $this->json(['status' => 'Task status updated'], Response::HTTP_OK);
    }
}
