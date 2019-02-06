<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\ToolInstance\Command\CloneToolInstanceCommand;
use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;
use App\Domain\ToolInstance\Command\UpdateToolInstanceCommand;
use App\Model\User;
use App\Model\Command;
use App\Domain\User\Command\ArchiveUserCommand;
use App\Domain\User\Command\ChangeUsernameCommand;
use App\Domain\User\Command\ChangeUserPasswordCommand;
use App\Domain\User\Command\ChangeUserProfileCommand;
use App\Domain\User\Command\CreateUserCommand;
use App\Domain\User\Command\DeleteUserCommand;
use App\Domain\User\Command\ReactivateUserCommand;
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

    /** @var array */
    private $availableCommands = [];

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
    public function messagebox(Request $request): JsonResponse
    {
        $this->availableCommands = [
            'archiveUser' => ArchiveUserCommand::class,
            'changeUsername' => ChangeUsernameCommand::class,
            'changeUserPassword' => ChangeUserPasswordCommand::class,
            'changeUserProfile' => ChangeUserProfileCommand::class,
            'deleteUser' => DeleteUserCommand::class,
            'reactivateUser' => ReactivateUserCommand::class,
            'cloneToolInstance' => CloneToolInstanceCommand::class,
            'createToolInstance' => CreateToolInstanceCommand::class,
            'updateToolInstance' => UpdateToolInstanceCommand::class,
        ];

        try {
            $commandClass = $this->extractCommandClass($request);
            $payload = $this->extractPayload($request);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 322);
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

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {

        $this->availableCommands = [
            'createUser' => CreateUserCommand::class
        ];

        try {
            $commandClass = $this->extractCommandClass($request);
            $payload = $this->extractPayload($request);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 322);
        }

        /** @var Command $command */
        $command = $commandClass::fromPayload($payload);
        $this->commandBus->dispatch($command);
        return new JsonResponse([], 202);
    }

    /**
     * @param Request $request
     * @return Command
     * @throws \Exception
     */
    private function extractCommandClass(Request $request): string
    {

        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            throw new \RuntimeException('Expecting Header: Content-Type: application/json');
        }

        $body = \json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON received.');
        }

        $message_name = $body['message_name'] ?? null;

        if (!$message_name) {
            throw new \Exception(sprintf('Unknown Message Name: %s', $message_name));
        }

        /** @var string $commandClass */
        $commandClass = $this->availableCommands[$message_name] ?? null;

        if (!$commandClass) {
            throw new \Exception(sprintf('Unknown Message Name: %s', $message_name));
        }

        return $commandClass;
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    private function extractPayload(Request $request): array
    {
        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            throw new \Exception('Expecting Header: Content-Type: application/json');
        }

        $body = \json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON received.');
        }

        $payload = $body['payload'] ?? null;

        if (null === $payload) {
            throw new \Exception('Parameter payload expected.');
        }

        if (!is_array($payload)) {
            throw new \Exception('Payload is expected to be an object or array.');
        }

        return $payload;
    }
}
