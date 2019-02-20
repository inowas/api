<?php

namespace App\Controller;

use App\Model\DataDrop;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class DataDropperController
{

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var Filesystem */
    private $filesystem;


    public function __construct(TokenStorageInterface $tokenStorage, Filesystem $filesystem, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/datadropper", name="data_dropper_post_data", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \League\Flysystem\FileExistsException
     * @throws \Exception
     */
    public function postData(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $content = $request->getContent();
        $hash = hash('sha1', $content, false);

        $dataDrop = $this->entityManager->getRepository(DataDrop::class)->findOneBy(['hash' => $hash]);

        if ($dataDrop instanceof DataDrop) {
            return new JsonResponse(['filename' => $hash]);
        }

        $this->filesystem->write($hash, $content);

        $dataDrop = new DataDrop($hash, $user, $hash, 'local');
        $this->entityManager->persist($dataDrop);
        $this->entityManager->flush();

        return new JsonResponse(['filename' => $hash]);
    }

    /**
     * @Route("/datadropper/{filename}", name="data_dropper_get_data", methods={"GET"})
     * @param string $filename
     * @return Response
     */
    public function getData(string $filename): Response
    {

        /** @var DataDrop $dataDrop */
        $dataDrop = $this->entityManager->getRepository(DataDrop::class)->findOneBy(['hash' => $filename]);

        if (!$dataDrop instanceof DataDrop) {
            return new Response('Not Found', 404);
        }

        try {
            $content = $this->filesystem->read($dataDrop->filename());
        } catch (\League\Flysystem\FileNotFoundException $exception) {
            return new Response('Not Found', 404);
        }

        return new Response($content, 200);
    }
}
