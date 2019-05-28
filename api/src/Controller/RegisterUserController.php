<?php

namespace App\Controller;

use App\Domain\User\Command\SignupUserCommand;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use TweedeGolf\PrometheusClient\CollectorRegistry;


class RegisterUserController
{

    /** @var CollectorRegistry */
    private $collectorRegistry;

    /** @var MessageBusInterface */
    private $commandBus;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        MessageBusInterface $bus,
        EntityManagerInterface $entityManager,
        CollectorRegistry $collectorRegistry
    )
    {
        $this->collectorRegistry = $collectorRegistry;
        $this->commandBus = $bus;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function register(Request $request): JsonResponse
    {
        //$sendMail = false;
        //$origin = $request->headers->get('origin');
        //if (strpos($origin, 'inowas.com') === false) {
        //    $sendMail = true;
        //}

        $metric = $this->collectorRegistry->getCounter('http_requests_total');
        $metric->inc(1, ['handler' => '/register']);

        $content = json_decode($request->getContent(), true);

        if (!array_key_exists('name', $content)) {
            return new JsonResponse(['message' => 'Prop name expected.'], 322);
        }

        $name = $content['name'];

        if (!array_key_exists('email', $content)) {
            return new JsonResponse(['message' => 'Prop email expected.'], 322);
        }

        $email = $content['email'];

        if (!array_key_exists('password', $content)) {
            return new JsonResponse(['message' => 'Prop password expected.'], 322);
        }

        $password = $content['password'];

        // check if user with username or email exists
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $email]);
        if ($user instanceof User) {
            return new JsonResponse(['message' => 'User already exists.'], 322);
        }

        // Send confirmationMail
        //if ($sendMail) {
            // send mail
        //}

        $signupUserCommand = SignupUserCommand::fromParams($name, $email, $password);
        $this->commandBus->dispatch($signupUserCommand);
        return new JsonResponse([], 202);
    }
}
