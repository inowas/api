<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\ToolInstance\Command\AddBoundaryCommand;
use App\Domain\ToolInstance\Command\AddLayerCommand;
use App\Domain\ToolInstance\Command\CloneLayerCommand;
use App\Domain\ToolInstance\Command\CloneModflowModelCommand;
use App\Domain\ToolInstance\Command\CloneToolInstanceCommand;
use App\Domain\ToolInstance\Command\CreateModflowModelCommand;
use App\Domain\ToolInstance\Command\CreateToolInstanceCommand;
use App\Domain\ToolInstance\Command\DeleteModflowModelCommand;
use App\Domain\ToolInstance\Command\DeleteToolInstanceCommand;
use App\Domain\ToolInstance\Command\McdaDeleteCriterionCommand;
use App\Domain\ToolInstance\Command\McdaUpdateCriterionCommand;
use App\Domain\ToolInstance\Command\McdaUpdateProjectCommand;
use App\Domain\ToolInstance\Command\RemoveBoundaryCommand;
use App\Domain\ToolInstance\Command\RemoveLayerCommand;
use App\Domain\ToolInstance\Command\UpdateBoundaryCommand;
use App\Domain\ToolInstance\Command\UpdateLayerCommand;
use App\Domain\ToolInstance\Command\UpdateModflowModelCalculationIdCommand;
use App\Domain\ToolInstance\Command\UpdateModflowModelMetadataCommand;
use App\Domain\ToolInstance\Command\UpdateModflowModelDiscretizationCommand;
use App\Domain\ToolInstance\Command\UpdateFlopyPackagesCommand;
use App\Domain\ToolInstance\Command\UpdateSoilmodelPropertiesCommand;
use App\Domain\ToolInstance\Command\UpdateStressperiodsCommand;
use App\Domain\ToolInstance\Command\UpdateToolInstanceCommand;
use App\Domain\ToolInstance\Command\UpdateToolInstanceDataCommand;
use App\Domain\ToolInstance\Command\UpdateToolInstanceMetadataCommand;
use App\Model\User;
use App\Model\Command;
use App\Domain\User\Command\ArchiveUserCommand;
use App\Domain\User\Command\ChangeUsernameCommand;
use App\Domain\User\Command\ChangeUserPasswordCommand;
use App\Domain\User\Command\ChangeUserProfileCommand;
use App\Domain\User\Command\CreateUserCommand;
use App\Domain\User\Command\DeleteUserCommand;
use App\Domain\User\Command\ReactivateUserCommand;
use Swaggest\JsonSchema\Exception;
use Swaggest\JsonSchema\InvalidValue;
use Swaggest\JsonSchema\Schema;
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

            AddBoundaryCommand::class,
            AddLayerCommand::class,
            CloneLayerCommand::class,
            CloneModflowModelCommand::class,
            CloneToolInstanceCommand::class,
            CreateModflowModelCommand::class,
            CreateToolInstanceCommand::class,
            DeleteModflowModelCommand::class,
            DeleteToolInstanceCommand::class,
            McdaDeleteCriterionCommand::class,
            McdaUpdateCriterionCommand::class,
            McdaUpdateProjectCommand::class,
            RemoveBoundaryCommand::class,
            RemoveLayerCommand::class,
            UpdateBoundaryCommand::class,
            UpdateFlopyPackagesCommand::class,
            UpdateLayerCommand::class,
            UpdateModflowModelCalculationIdCommand::class,
            UpdateModflowModelDiscretizationCommand::class,
            UpdateModflowModelMetadataCommand::class,
            UpdateSoilmodelPropertiesCommand::class,
            UpdateStressperiodsCommand::class,
            UpdateToolInstanceCommand::class,
            UpdateToolInstanceDataCommand::class,
            UpdateToolInstanceMetadataCommand::class
        ];

        $this->setAvailableCommands($availableCommands);

        try {
            $this->assertIsValidRequest($request);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 322);
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        # extract message
        $message = $this->getMessage($request);
        $messageName = $message['message_name'];
        $payload = $message['payload'];

        /** @var Command $commandClass */
        $commandClass = $this->availableCommands[$messageName];

        try {
            $commandClass::getJsonSchema() && $this->validateSchema($commandClass::getJsonSchema(), $request->getContent());
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 322);
        }

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

        $message = $this->getMessage($request);
        $messageName = $message['message_name'];
        $payload = $message['payload'];

        /** @var Command $commandClass */
        $commandClass = $this->availableCommands[$messageName];

        try {
            $commandClass::getJsonSchema() && $this->validateSchema($commandClass::getJsonSchema(), $request->getContent());
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 322);
        }

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

    private function getMessage(Request $request): array
    {
        return json_decode($request->getContent(), true);
    }

    /**
     * @param $schema
     * @param $content
     * @throws Exception
     * @throws InvalidValue
     */
    private function validateSchema(string $schema, string $content): void
    {
        $schema = Schema::import($schema);
        $schema->in(json_decode($content));
    }
}
