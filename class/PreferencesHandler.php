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

class PreferencesHandler extends Myservices\ServiceORM
{
    /**
     * PreferencesHandler constructor.
     * @param $db
     */
    public function __construct($db)
    {    //                             Table               Classe          Id
        parent::__construct($db, 'myservices_prefs', Preferences::class, 'prefs_id');
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
        $hour    = \XoopsModules\Myservices\Utilities::normalyzeTime($hour);
        $now     = time();
        $hours   = (int)mb_substr($hour, 0, 2);
        $minutes = (int)mb_substr($hour, 3, 2);
        $seconds = (int)mb_substr($hour, 6, 2);
        $later   = mktime($hours, $minutes, $seconds, $month, $day, $year);
        if ($later < $now) {
            return false;
        }
        $latence    = \XoopsModules\Myservices\Utilities::getModuleOption('latence') * 3600;
        $difference = $later - $now;
        if ($difference < $latence) {
            return false;
        }

        return true;
    }

    /**
     * Renvoie la date à partir de laquelle on peut commander
     *
     * @return int timestamp qui indique le premier jour d'ouverture disponible
     */
    public function getFirstAvailableDayForOrder()
    {
        global $hMsCalendar;
        $now          = time();
        $latence      = \XoopsModules\Myservices\Utilities::getModuleOption('latence') * 3600;
        $true         = false;
        $startingHour = $this->getLowerOpenTime();
        while (!$true) {
            $now     += $latence;
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
     * @param int    $year  Année à de la date
     * @param  int   $month Mois de la date
     * @param  int   $day   Jour de la date
     * @param string $hour  L'heure à tester (hh:mm:ss)
     * @param bool   $strict
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

        $hour = \XoopsModules\Myservices\Utilities::normalyzeTime($hour);

        if (!$strict) {
            if ('00:00:00' !== $preference->getVar($name1) && '00:00:00' !== $preference->getVar($name2)) {
                if ($hour >= $preference->getVar($name1) && $hour <= $preference->getVar($name2)) {
                    return true;
                }
            }

            if ('00:00:00' !== $preference->getVar($name3) && '00:00:00' !== $preference->getVar($name4)) {
                if ($hour >= $preference->getVar($name3) && $hour <= $preference->getVar($name4)) {
                    return true;
                }
            }
        } else {
            if ('00:00:00' !== $preference->getVar($name1) && '00:00:00' !== $preference->getVar($name2)) {
                if ($hour >= $preference->getVar($name1) && $hour < $preference->getVar($name2)) {
                    return true;
                }
            }

            if ('00:00:00' !== $preference->getVar($name3) && '00:00:00' !== $preference->getVar($name4)) {
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
        $items    = [];
        $criteria = new \Criteria('prefs_id', 0, '<>');

        $items = $this->getObjects($criteria);
        if (0 == count($items)) {
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
                if ('00:00:00' !== $preference->getVar($name) && $preference->getVar($name) <= $minHour) {
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
                if ('00:00:00' !== $preference->getVar($name) && $preference->getVar($name) >= $maxHour) {
                    $maxHour = $preference->getVar($name);
                }
            }
        }

        return $maxHour;
    }
}
