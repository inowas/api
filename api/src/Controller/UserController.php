<?php

namespace App\Controller;

use App\Model\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use TweedeGolf\PrometheusClient\CollectorRegistry;
use TweedeGolf\PrometheusClient\PrometheusException;


class UserController
{

    /** @var CollectorRegistry */
    private $collectorRegistry;

    /** @var TokenStorageInterface */
    private $tokenStorage;


    public function __construct(TokenStorageInterface $tokenStorage, CollectorRegistry $collectorRegistry)
    {
        $this->collectorRegistry = $collectorRegistry;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/user", name="user", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws PrometheusException
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(null, 401);
        }

        $metric = $this->collectorRegistry->getCounter('http_requests_total');
        $metric->inc(1, ['handler' => '/user']);

        $response = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'profile' => $user->getProfile(),
            'enabled' => $user->isEnabled(),
        ];

        return new JsonResponse($response);
    }
}
