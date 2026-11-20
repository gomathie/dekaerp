<?php

namespace Webkul\Support\Http\Controllers;

use Exception;
use Illuminate\Http\Response as IlluminateResponse;

class ImageCacheController
{
    /**
     * Path to the local brand logo, relative to the public directory.
     *
     * @var string
     */
    const LOGO_PATH = 'images/logo.svg';

    /**
     * Get HTTP response of the local brand logo.
     *
     * @param  string  $filename
     * @return Illuminate\Http\Response
     */
    public function getImage($filename)
    {
        try {
            $content = base64_encode(file_get_contents(public_path(self::LOGO_PATH)));
        } catch (Exception $e) {
            $content = '';
        }

        return $this->buildResponse($content);
    }

    /**
     * Builds HTTP response from given image data
     *
     * @param  string  $content
     * @return Illuminate\Http\Response
     */
    protected function buildResponse($content)
    {
        $decodedContent = base64_decode($content);

        /**
         * Define mime type
         */
        $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $decodedContent);

        /**
         * Respond with 304 not modified if browser has the image cached
         */
        $eTag = md5($decodedContent);

        $notModified = isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $eTag;

        $responseContent = $notModified ? null : $decodedContent;

        $statusCode = $notModified ? 304 : 200;

        /**
         * Return http response
         */
        return new IlluminateResponse($responseContent, $statusCode, [
            'Content-Type'   => $mime,
            'Cache-Control'  => 'max-age=10080, public',
            'Content-Length' => strlen($responseContent),
            'Etag'           => $eTag,
        ]);
    }
}
