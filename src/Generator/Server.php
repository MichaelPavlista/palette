<?php

/**
 * This file is part of the Palette (https://github.com/MichaelPavlista/palette)
 * Copyright (c) 2016 Michael Pavlista (http://www.pavlista.cz/)
 *
 * @author Michael Pavlista
 * @email  michael@pavlista.cz
 * @link   http://pavlista.cz/
 * @link   https://www.facebook.com/MichaelPavlista
 * @copyright 2016
 */

namespace Palette\Generator;

use Palette\Picture;
use Palette\Exception;
use Palette\Utils\Security;

/**
 * Class Server
 * Implementation of IPictureGenerator which generates the desired image variants on remote server.
 * @package Palette\Generator
 */
class Server extends CurrentExecution implements IServerGenerator
{
    /** @var string */
    private $signingKey;


    /**
     * Server constructor.
     * @param string $storagePath absolute or relative path to directory for storage generated image variants
     * @param string $storageUrl absolute url to directory of generated images
     * @param string|null $basePath path to website directory root (see documentation)
     * @param string $signingKey
     * @throws Exception
     */
    public function __construct($storagePath, $storageUrl, $basePath, $signingKey)
    {
        parent::__construct($storagePath, $storageUrl, $basePath);

        if(!is_string($signingKey) || !$signingKey)
        {
            throw new Exception('Signing key must be non-empty string');
        }

        $this->signingKey = $signingKey;
    }


    /**
     * Save picture variant to generator storage.
     * Server generator does't save itself.
     * @param Picture $picture
     * @return void
     */
    public function save(Picture $picture)
    {
        return NULL;
    }


    /**
     * Returns file path of the image file variant.
     * Does't verify if the file physically exists.
     * @param Picture $picture
     * @return string
     */
    public function getPath(Picture $picture)
    {
        $storagePath = str_replace(
            $this->basePath,
            DIRECTORY_SEPARATOR,
            pathinfo($picture->getImage(), PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR
        );

        return $this->unifyPath($this->storagePath . '/' . $storagePath . '/' . $this->getFileName($picture));
    }


    /**
     * Returns the absolute URL of the image to the desired variant.
     * @param Picture $picture
     * @return string
     */
    public function getUrl(Picture $picture)
    {
        $file = $this->getPath($picture);

        $url = str_replace(
            $this->basePath,
            DIRECTORY_SEPARATOR,
            pathinfo($picture->getImage(), PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR
        );

        $url = $this->unifyPath($url, '/');
        $url = preg_replace('/([^:])(\/{2,})/', '$1/', $this->storageUrl . '/' . $url . '/' . $this->getFileName($picture));

        // BUILD VARIANT URL
        $variantActual = $this->isFileActual($file, $picture);

        if ($variantActual === FALSE)
        {
            $queryString = $picture->getImage() . '@' . $picture->getImageQuery();

            return $url . '?imageQuery=' . Security::signPaletteQuery($queryString, $this->signingKey);
        }

        if($variantActual === NULL)
        {
            $queryString = $picture->getImage() . '@' . $picture->getImageQuery();

            $this->requestWithoutWaiting(

                $this->storageUrl . 'palette-server.php',
                array('regenerate' => Security::signPaletteQuery($queryString, $this->signingKey))
            );
        }

        return $url;
    }


    /**
     * Execute server generator backend.
     * @return void
     * @throws Exception
     */
    public function serverResponse()
    {
        if(!empty($_GET['imageQuery']))
        {
            $paletteQuery = Security::validateSignedPaletteQuery($_GET['imageQuery'], $this->signingKey);

            $picture  = $this->loadPicture($paletteQuery);
            $savePath = $this->getPath($picture);

            $picture->save($savePath);

            if($savePath !== $this->getPath($picture))
            {
                throw new Exception('Picture effect is changing its own arguments on effect apply');
            }

            $picture->output();
        }

        if(!empty($_POST['regenerate']))
        {
            $paletteQuery = Security::validateSignedPaletteQuery($_POST['regenerate'], $this->signingKey);

            $picture = $this->loadPicture($paletteQuery);
            $picture->save($this->getPath($picture));
        }
    }


    /**
     * Sends the POST request without waiting for a response.
     * @param string $url
     * @param array $params
     */
    protected function requestWithoutWaiting($url, array $params = array())
    {
        $post = array();

        foreach($params as $index => $val)
        {
            $post[] = $index . '=' . urlencode($val);
        }

        $post = implode('&', $post);

        $request = parse_url($url);

        // SUPPORT FOR RELATIVE GENERATOR URL
        if(!isset($request['host']))
        {
            $request['host'] = $_SERVER['HTTP_HOST'];
        }

        if(!isset($request['port']))
        {
            $request['port'] = $_SERVER['SERVER_PORT'];
        }

        // SEND REQUEST WITHOUT WAITING
        $protocol = isset($_SERVER['HTTPS']) ? 'ssl://' : '';
        $safePort = isset($_SERVER['HTTPS']) ? 443 : 80;

        // HTTPS WORKAROUND
        if($request['port'] == 80 && $safePort == 443)
        {
            $request['port'] = $safePort;
        }

        $fp = fsockopen($protocol . $request['host'], $request['port'], $errNo, $errStr, 60);

        $command  = "POST " . $request['path'] . " HTTP/1.1\r\n";
        $command .= "Host: " . $request['host'] . "\r\n";
        $command .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $command .= "Content-Length: " . strlen($post) . "\r\n";
        $command .= "Connection: Close\r\n\r\n";

        if($post)
        {
            $command .= $post;
        }

        fwrite($fp, $command);
        fclose($fp);
    }
}
