<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\ToolInstance\Command\CloneToolInstanceCommand;
use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;
use App\Domain\ToolInstance\Command\DeleteToolInstanceCommand;
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
        $availableCommands = [
            ArchiveUserCommand::class,
            ChangeUsernameCommand::class,
            ChangeUserPasswordCommand::class,
            ChangeUserProfileCommand::class,
            DeleteUserCommand::class,
            ReactivateUserCommand::class,
            CloneToolInstanceCommand::class,
            CreateToolInstanceCommand::class,
            DeleteToolInstanceCommand::class,
            UpdateToolInstanceCommand::class,
        ];

        $this->setAvailableCommands($availableCommands);

        try {
            $this->assertIsValidRequest($request);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 322);
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $message_name = $this->getMessageName($request);
        $payload = $this->getPayload($request);
        $commandClass = $this->availableCommands[$message_name];

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
     * @throws \Exception
     */
    public function register(Request $request): JsonResponse
    {
        $availableCommands = [
            CreateUserCommand::class
        ];

        $this->setAvailableCommands($availableCommands);

        try {
            $this->assertIsValidRequest($request);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 322);
        }

        $message_name = $this->getMessageName($request);
        $payload = $this->getPayload($request);
        $commandClass = $this->availableCommands[$message_name];

        /** @var Command $command */
        $command = $commandClass::fromPayload($payload);
        $this->commandBus->dispatch($command);
        return new JsonResponse([], 202);
    }

    private function setAvailableCommands(array $availableCommands): void
    {
        foreach ($availableCommands as $command) {
            $this->availableCommands[$command::getMessageName()] = $command;
        }
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    private function assertIsValidRequest(Request $request): void
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
            throw new \Exception(sprintf('Parameter message_name not given or null.'));
        }

        if (!array_key_exists($message_name, $this->availableCommands)) {
            throw new \Exception(
                sprintf(
                    'MessageName: %s not in the list of available commands. Available commands are: %s.',
                    $message_name, implode(', ', array_keys($this->availableCommands))
                )
            );
        }

        $payload = $body['payload'] ?? null;

        if (null === $payload) {
            throw new \Exception('Parameter payload expected.');
        }
    }

    private function getMessageName(Request $request): string
    {
        return json_decode($request->getContent(), true)['message_name'];
    }

    private function getPayload(Request $request): array
    {
        return json_decode($request->getContent(), true)['payload'];
    }
}
