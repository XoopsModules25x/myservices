<?php namespace XoopsModules\Myservices;

/**
 * ****************************************************************************
 * catads - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class ServiceObject
 * @package XoopsModules\Myservices
 */
class ServiceObject extends \XoopsObject
{
    /**
     * @param string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret = [];
        foreach ($this->vars as $k => $v) {
            $ret[$k] = $this->getVar($k, $format);
        }

        return $ret;
    }

    // TODO: Rajouter une méthode intsert() et delete()

    /**
     * Permet de valoriser un champ de la table comme si c'était une propriété de la classe
     *
     * @example $enregistrement->nom_du_champ = 'ma chaine'
     *
     * @param string $key   Le nom du champ à traiter
     * @param mixed  $value La valeur à lui attribuer
     */
    public function __set($key, $value)
    {
        return $this->setVar($key, $value);
    }

    /**
     * Permet d'accéder aux champs de la table comme à des propriétés de la classe
     *
     * @example echo $enregistrement->nom_du_champ;
     *
     * @param string $key Le nom du champ que l'on souhaite récupérer
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getVar($key);
    }
}
