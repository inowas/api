<?php

namespace App\Controller;

use App\Model\User;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class DataDropperController
{

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var Filesystem */
    private $filesystem;


    public function __construct(TokenStorageInterface $tokenStorage, Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/datadropper", name="data_dropper_post_data", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \League\Flysystem\FileExistsException
     */
    public function postData(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $content = $request->getContent();
        $hash = hash('sha1', $content, false);
        $this->filesystem->write($hash, $content);

        return new JsonResponse(['filename' => $hash]);
    }

    /**
     * @Route("/datadropper/{filename}", name="data_dropper_get_data", methods={"GET"})
     * @param string $filename
     * @return JsonResponse
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getData(string $filename): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $content = $this->filesystem->read($filename);
        return new JsonResponse([$content]);
    }
}
