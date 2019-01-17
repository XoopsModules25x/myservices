<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Classe chargée de gérer les dates de fermeture habituelles du magasin
 */
//require XOOPS_ROOT_PATH.'/kernel/object.php';
if (!class_exists('myservices_ORM')) {
    require XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
}

class myservices_prefs extends myservices_Object
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
        $ret = array();
        for ($i = 1; $i <= 7; ++$i) {
            $debut1 = 'prefs_j' . $i . 't1debut';
            $fin1   = 'prefs_j' . $i . 't1fin';
            $debut2 = 'prefs_j' . $i . 't2debut';
            $fin2   = 'prefs_j' . $i . 't2fin';
            if ($this->getVar($debut1, 'e') === '00:00:00' && $this->getVar($fin1, 'e') === '00:00:00' && $this->getVar($debut2, 'e') === '00:00:00' && $this->getVar($fin2, 'e') == '00:00:00') {
                $ret[] = $i;
            }
        }

        return $ret;
    }
}

class MyservicesMyservices_prefsHandler extends myservices_ORM
{
    public function __construct($db)
    {    //                             Table               Classe          Id
        parent::__construct($db, 'myservices_prefs', 'myservices_prefs', 'prefs_id');
    }

    /**
     * Indique si on peut passer commande, par rapport à la date actuelle et par rapport à la date de réservation
     * @param $year
     * @param $month
     * @param $day
     * @param $hour
     * @return bool
     */
    public function canOrderNow($year, $month, $day, $hour)
    {
        $hour    = myservices_utils::normalyzeTime($hour);
        $now     = time();
        $hours   = (int)substr($hour, 0, 2);
        $minutes = (int)substr($hour, 3, 2);
        $seconds = (int)substr($hour, 6, 2);
        $later   = mktime($hours, $minutes, $seconds, $month, $day, $year);
        if ($later < $now) {
            return false;
        }
        $latence    = myservices_utils::getModuleOption('latence') * 3600;
        $difference = $later - $now;
        if ($difference < $latence) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Renvoie la date à partir de laquelle on peut commander
     *
     * @return integer timestamp qui indique le premier jour d'ouverture disponible
     */
    public function getFirstAvailableDayForOrder()
    {
        global $hMsCalendar;
        $now          = time();
        $latence      = myservices_utils::getModuleOption('latence') * 3600;
        $true         = false;
        $startingHour = $this->getLowerOpenTime();
        while (!$true) {
            $now += $latence;
            $newDate = getdate($now);
            if ($this->isStoreOpen($newDate['year'], $newDate['mon'], $newDate['mday'], $startingHour)) {
                if (!$hMsCalendar->isStoreClosed($newDate['year'], $newDate['mon'], $newDate['mday'])) {
                    return $now;
                }
            }
        }
    }

    /**
     * Fonction chargée d'indiquer si le magasin est ouvert à une date et à une heure donnée (pour les horaires habituels du magasin)
     *
     * @param integer  $year  Année à de la date
     * @param  integer $month Mois de la date
     * @param  integer $day   Jour de la date
     * @param string   $hour  L'heure à tester (hh:mm:ss)
     * @param bool     $strict
     * @return bool Vrai = magasin ouvert, False = magasin fermé
     */
    public function isStoreOpen($year, $month, $day, $hour, $strict = false)
    {
        $preference = $this->getPreference();
        $timestamp  = mktime(1, 1, 1, $month, $day, $year);
        $weekday    = date('N', $timestamp);    // Jour de la semaine de 1 (lundi) à 7 (dimanche)
        $name1      = 'prefs_j' . $weekday . 't1debut';
        $name2      = 'prefs_j' . $weekday . 't1fin';
        $name3      = 'prefs_j' . $weekday . 't2debut';
        $name4      = 'prefs_j' . $weekday . 't2fin';

        $hour = myservices_utils::normalyzeTime($hour);

        if (!$strict) {
            if ($preference->getVar($name1) !== '00:00:00' && $preference->getVar($name2) !== '00:00:00') {
                if ($hour >= $preference->getVar($name1) && $hour <= $preference->getVar($name2)) {
                    return true;
                }
            }

            if ($preference->getVar($name3) !== '00:00:00' && $preference->getVar($name4) !== '00:00:00') {
                if ($hour >= $preference->getVar($name3) && $hour <= $preference->getVar($name4)) {
                    return true;
                }
            }
        } else {
            if ($preference->getVar($name1) !== '00:00:00' && $preference->getVar($name2) !== '00:00:00') {
                if ($hour >= $preference->getVar($name1) && $hour < $preference->getVar($name2)) {
                    return true;
                }
            }

            if ($preference->getVar($name3) !== '00:00:00' && $preference->getVar($name4) !== '00:00:00') {
                if ($hour >= $preference->getVar($name3) && $hour < $preference->getVar($name4)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Renvoie l'unique enregistrement qui contient les préférences du site (et le crée avec des données vierges s'il n'existe pas)
     *
     * @return object L'objet préférences
     */
    public function getPreference()
    {
        $items    = array();
        $criteria = new Criteria('prefs_id', 0, '<>');

        $items = $this->getObjects($criteria);
        if (count($items) == 0) {
            $items[0] = $this->create(true);
            $this->forceCacheClean();
        }

        return $items[0];
    }

    /**
     * Renvoie l'heure d'ouverture la plus matinale
     *
     * @return string L'heure d'ouverture la plus matinale
     */
    public function getLowerOpenTime()
    {
        $minHour    = '23:59:59';
        $preference = $this->getPreference();
        for ($i = 1; $i <= 7; ++$i) {
            for ($j = 1; $j <= 2; ++$j) {
                $name = 'prefs_j' . $i . 't' . $j . 'debut';
                if ($preference->getVar($name) !== '00:00:00' && $preference->getVar($name) <= $minHour) {
                    $minHour = $preference->getVar($name);
                }
            }
        }

        return $minHour;
    }

    /**
     * Renvoie l'heure de fermeture la plus tardive
     *
     * @return string L'heure de fermeture la plus tardive
     */
    public function getUpperOpenTime()
    {
        $maxHour    = '00:00:00';
        $preference = $this->getPreference();
        for ($i = 1; $i <= 7; ++$i) {
            for ($j = 1; $j <= 2; ++$j) {
                $name = 'prefs_j' . $i . 't' . $j . 'fin';
                if ($preference->getVar($name) !== '00:00:00' && $preference->getVar($name) >= $maxHour) {
                    $maxHour = $preference->getVar($name);
                }
            }
        }

        return $maxHour;
    }
}
