<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Model\Common\Command;
use App\Model\User\Command\CreateUserCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class MessageBoxController
{
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    private $availableCommands = [
        'createUser' => CreateUserCommand::class
    ];

    public function __construct(MessageBusInterface $bus, TokenStorageInterface $tokenStorage)
    {
        $this->commandBus = $bus;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/messagebox", name="messagebox", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            return new JsonResponse(['message' => 'Expecting Header: Content-Type: application/json'], 322);
        }

        $body = \json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['message' => 'Invalid JSON received.'], 322);
        }

        $message_name = $body['message_name'] ?? null;

        if (!$message_name) {
            return new JsonResponse(['message' => sprintf('Unknown Message Name: %s', $message_name)], 322);
        }

        $commandClass = $this->availableCommands[$message_name] ?? null;

        if (!$commandClass) {
            return new JsonResponse(['message' => sprintf('Unknown Message Name: %s', $message_name)], 322);
        }

        $payload = $body['payload'] ?? null;

        if (!$payload) {
            return new JsonResponse(['message' => 'Parameter payload expected.'], 322);
        }

        if (!is_array($payload)) {
            return new JsonResponse(['message' => 'Payload is expected to be an object or array.'], 322);
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        /** @var Command $command */
        $command = $commandClass::fromPayload($payload);
        $command->withAddedMetadata('user_id', $user->getId()->toString());
        $command->withAddedMetadata('is_admin', in_array('ROLE_ADMIN', $user->getRoles()));

        $this->commandBus->dispatch($command);
        return new JsonResponse([], 202);
    }
}
