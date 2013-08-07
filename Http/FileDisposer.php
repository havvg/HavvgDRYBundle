<?php

namespace Havvg\Bundle\DRYBundle\Http;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileDisposer
{
    /**
     * Add response headers to dispose the given file.
     *
     * @param string   $filename
     * @param string   $content
     * @param string   $contentType
     * @param string   $disposition
     * @param Response $response
     *
     * @return Response
     */
    public static function dispose($filename, $content, $contentType, $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        $response->setContent($content);

        $response->headers->add([
            'Content-Type' => $contentType,
            'Content-Length' => strlen($content),
            'Content-Disposition' => $response->headers->makeDisposition($disposition, $filename),
        ]);

        return $response;
    }
}
