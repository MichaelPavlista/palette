<?php

namespace Palette;

/**
 * Class ServerStorage
 * @package Palette
 */
class ServerStorage extends PictureStorage {

    /**
     * Ověří zda obrázek v úložišti je aktuální
     * @param $file
     * @return bool
     */
    public function isFileActual($file, Picture $picture) {

        return @filemtime($file) === @filemtime($picture->getImage());
    }


    /**
     * Ověří zda obrázek již v úložišti fyzicky existuje
     * @param $file
     * @return bool
     */
    public function isFileExists($file) {

        return file_exists($file);
    }


    /**
     * Uloží obrázek do úložiště
     * @param Picture $picture
     * @return mixed
     */
    public function save(Picture $picture) {

        return NULL;
    }


    /**
     * Vrací cestu k obrázku v úložišti, neověřuje zda obrázek fyzicky existuje.
     * @param Picture $picture
     * @return string absolutní cesta k obrázku
     */
    public function getFile(Picture $picture) {

        $storagePath = str_replace($this->basePath, DIRECTORY_SEPARATOR, pathinfo($picture->getImage(), PATHINFO_DIRNAME) . '/');

        return $this->unifyPath($this->path . '/' . $storagePath . '/' . $this->getFileName($picture));
    }


    /**
     * Vrací absolutní url adresu k obrázku v úložišti
     * @param Picture $picture
     * @return string
     */
    public function getUrl(Picture $picture) {

        $file = $this->getFile($picture);

        $url = str_replace($this->basePath, DIRECTORY_SEPARATOR, pathinfo($picture->getImage(), PATHINFO_DIRNAME) . '/');
        $url = $this->unifyUrl($this->url . '/' . $url . '/' . $this->getFileName($picture));

        if(!$this->isFileActual($file, $picture) && !$this->isFileExists($file)) {

            return $url . '?imageQuery=' . urlencode($picture->getImage() . '@' . $picture->getImageQuery());
        }
        elseif(!$this->isFileActual($file, $picture) && $this->isFileExists($file)) {

            $this->requestWithoutWaiting($this->url . 'palette-server.php', array('regenerate' => $picture->getImage() . '@' . $picture->getImageQuery()));
        }

        return $url;
    }


    /**
     * Vrací výsledek serveru pro vzdálené generování obrázků
     */
    public function serverResponse() {

        if(!empty($_GET['imageQuery'])) {

            $picture = $this->loadPicture($_GET['imageQuery']);
            $picture->save($this->getFile($picture));
            $picture->output();
        }

        if(!empty($_POST['regenerate'])) {

            $picture = $this->loadPicture($_POST['regenerate']);
            $picture->save($this->getFile($picture));
        }
    }


    /**
     * Zašle POST request bez čekání na odpověď od serveru
     * @param $url
     * @param $params
     */
    function requestWithoutWaiting($url, $params) {

        $post = array();

        foreach($params as $index => $val) {

            $post[] = $index . '=' . urlencode($val);
        }

        $post = implode('&', $post);
        $request = parse_url($url);

        $fp = fsockopen($request['host'], isset($request['port']) ? $request ['port'] : 80, $errNo, $errStr, 60);

        $command  = "POST " . $request['path'] . " HTTP/1.1\r\n";
        $command .= "Host: " . $request['host'] . "\r\n";
        $command .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $command .= "Content-Length: " . strlen($post) . "\r\n";
        $command .= "Connection: Close\r\n\r\n";

        if($post) {

            $command .= $post;
        }

        fwrite($fp, $command);
        fclose($fp);
    }

}