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

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Classe chargée de gérer les dates de fermeture habituelles du magasin
 */
//require_once XOOPS_ROOT_PATH.'/kernel/object.php';
//if (!class_exists('myservices_ORM')) {
//    require_once XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
//}

class Preferences extends Myservices\ServiceObject
{
    public function __construct()
    {
        $this->initVar('prefs_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('prefs_j1t1debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j1t1fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j1t2debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j1t2fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j2t1debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j2t1fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j2t2debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j2t2fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j3t1debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j3t1fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j3t2debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j3t2fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j4t1debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j4t1fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j4t2debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j4t2fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j5t1debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j5t1fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j5t2debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j5t2fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j6t1debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j6t1fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j6t2debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j6t2fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j7t1debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j7t1fin', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j7t2debut', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('prefs_j7t2fin', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * Renvoie la liste des jours où le magasin est fermé (dans ses horaires habituels)
     *
     * @return array Valeurs = indice des jours fermés (premier jour = lundi = 1)
     */
    public function getClosedDays()
    {
        $ret = [];
        for ($i = 1; $i <= 7; ++$i) {
            $debut1 = 'prefs_j' . $i . 't1debut';
            $fin1   = 'prefs_j' . $i . 't1fin';
            $debut2 = 'prefs_j' . $i . 't2debut';
            $fin2   = 'prefs_j' . $i . 't2fin';
            if ('00:00:00' === $this->getVar($debut1, 'e') && '00:00:00' === $this->getVar($fin1, 'e') && '00:00:00' === $this->getVar($debut2, 'e') && '00:00:00' === $this->getVar($fin2, 'e')) {
                $ret[] = $i;
            }
        }

        return $ret;
    }
}
