<?php

namespace XoopsModules\Myservices;

/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

/**
 * Class RegistryFile
 * @package XoopsModules\Myservices
 */
class RegistryFile
{
    public $filename;    // Nom du fichier à traiter

    /**
     * Access the only instance of this class
     *
     * @return object
     *
     * @static
     * @staticvar   object
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * RegistryFile constructor.
     * @param null $fichier
     */
    public function __construct($fichier = null)
    {
        $this->setfile($fichier);
    }

    /**
     * @param null $fichier
     */
    public function setfile($fichier = null)
    {
        if ($fichier) {
            $this->filename = XOOPS_UPLOAD_PATH . '/' . $fichier;
        }
    }

    /**
     * @param null $fichier
     * @return bool|string
     */
    public function getfile($fichier = null)
    {
        $fw = '';
        if (!$fichier) {
            $fw = $this->filename;
        } else {
            $fw = XOOPS_UPLOAD_PATH . '/' . $fichier;
        }
        if (file_exists($fw)) {
            return file_get_contents($fw);
        }

        return '';
    }

    /**
     * @param      $content
     * @param null $fichier
     * @return bool
     */
    public function savefile($content, $fichier = null)
    {
        $fw = '';
        if (!$fichier) {
            $fw = $this->filename;
        } else {
            $fw = XOOPS_UPLOAD_PATH . '/' . $fichier;
        }
        if (file_exists($fw)) {
            @unlink($fw);
        }
        $fp = fopen($fw, 'w') || exit('Error, impossible to create the file ' . $this->filename);
        fwrite($fp, $content);
        fclose($fp);

        return true;
    }
}
