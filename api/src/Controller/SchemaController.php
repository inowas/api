<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class SchemaController extends AbstractController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $schemaBasePath = __DIR__ . '/../../schema/';
        $realBase = realpath($schemaBasePath);

        try {
            $scandir = scandir($realBase);
        } catch (\Exception $e) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        return $this->render(
            'schema_folders.html.twig',
            ['path' => '/', 'scandir' => $scandir]
        );
    }

    /**
     * @param string $path
     * @return Response
     */
    public function withPath(string $path): Response
    {

        $schemaBasePath = __DIR__ . '/../../schema/';
        $realBase = realpath($schemaBasePath);
        $userPath = $schemaBasePath . $path;
        $realUserPath = realpath($userPath);

        if ($realUserPath === false || strpos($realUserPath, $realBase) !== 0) {
            # Directory Traversal!
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        if ($this->endsWith($path, '.json')) {
            try {
                $content = file_get_contents($realUserPath);
                return new JsonResponse($content, 200, [], true);
            } catch (\Exception $exception) {
                return new Response('Not found', Response::HTTP_NOT_FOUND);
            }
        }

        try {
            $scandir = scandir($realUserPath);
        } catch (\Exception $e) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        return $this->render(
            'schema_folders.html.twig',
            ['path' => $path, 'scandir' => $scandir]
        );
    }


    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

}
