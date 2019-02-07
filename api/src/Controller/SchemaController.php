<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

final class SchemaController
{
    /**
     * @param string $path
     * @return Response
     */
    public function index(string $path): Response
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
                return new Response(file_get_contents($realUserPath));
            } catch (\Exception $exception) {
                return new Response('Not found', Response::HTTP_NOT_FOUND);
            }
        }

        try {
            $scandir = scandir($realUserPath);
        } catch (\Exception $e) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        return new Response(implode(', ', array_diff($scandir, ['.', '..'])));
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
