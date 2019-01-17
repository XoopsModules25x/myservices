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

//require_once XOOPS_ROOT_PATH.'/kernel/object.php';
//if (!class_exists('myservices_ORM')) {
//    require_once XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
//}

/**
 * Class EmployeesHandler
 * @package XoopsModules\Myservices
 */
class EmployeesHandler extends Myservices\ServiceORM
{
    /**
     * EmployeesHandler constructor.
     * @param $db
     */
    public function __construct($db)
    {    //                         Table                   Classe                  Id          Description
        parent::__construct($db, 'myservices_employees', Employees::class, 'employees_id', 'employees_lastname');
    }

    /**
     * Renvoie la liste des personnes qui fournissent un service lié à un produit
     *
     * @param int $products_id Numéro du produit
     * @return array Objets de type employés (clé = ID employé, valeur = objet employé)
     */
    public function getEmployeesForProduct($products_id)
    {
        global $hMsEmployeesProducts;
        $peopleList = $employees = [];
        $peopleList = $hMsEmployeesProducts->getEmployeesIdForProduct($products_id);
        if (count($peopleList) > 0) {    // On a la liste de toutes les personnes, reste à prendre les personnes actives
            $criteriaCompo = new \CriteriaCompo();
            $criteriaCompo->add(new \Criteria('employees_isactive', 1, '='));
            $criteriaCompo->add(new \Criteria('employees_id', '(' . implode(',', $peopleList) . ')', 'IN'));
            $employees = $this->getObjects($criteriaCompo, true);
        }

        return $employees;
    }

    /**
     * Renvoie la liste des employés actifs
     *
     * @return array Objets de type employés
     */
    public function getActiveEmployees()
    {
        $critere = new \Criteria('employees_isactive', 1, '=');
        if (isset($this->identifierName) && '' != trim($this->identifierName)) {
            $order = $this->identifierName;
        } else {
            $order = $this->keyName;
        }
        $tblItems = [];
        $critere->setOrder($order);
        $tblItems = $this->getObjects($critere);

        return $tblItems;
    }

    /**
     * Renvoie la liste des produits dont une personne assume le service
     *
     * @param  int $employees_id
     * @return array  Objets de type produits
     */
    public function getServicesFromEmployee($employees_id)
    {
        global $hMsEmployeesProducts, $hMsProducts;
        $tblProductsIds = $tblProducts = [];
        $tblProductsIds = $hMsEmployeesProducts->getProductsFromEployee($employees_id);
        if (count($tblProductsIds) > 0) {
            $tblProducts = $hMsProducts->getOnlineProductsFromId($tblProductsIds);
        }

        return $tblProducts;
    }

    /**
     * Indique si une personne est en congés à une date donnée
     *
     * @param int    $year         Année
     * @param  int   $month        Mois
     * @param  int   $day          Jour
     * @param string $startingHour Heure de début
     * @param int    $duration     Durée en heures
     * @param int    $employees_id Identifiant de l'employé(e)
     * @return bool Vrai si la personne est en congés sinon faux
     */
    public function isEmployeeInHoliday($year, $month, $day, $startingHour, $duration, $employees_id)
    {
        global $hMsCalendar;
        $startingHour  = \XoopsModules\Myservices\Utilities::normalyzeTime($startingHour);
        $requestedDate = sprintf('%04d-%02d-%02d %s', $year, $month, $day, $startingHour);
        // 1) Recherche du "en plein dedans"
        $sql    = 'SELECT COUNT(*) AS cpt FROM '
                  . $this->db->prefix('myservices_calendar')
                  . ' WHERE calendar_status = '
                  . CALENDAR_STATUS_HOLIDAY
                  . ' AND calendar_employees_id = '
                  . (int)$employees_id
                  . " AND (calendar_start <= '"
                  . $requestedDate
                  . "' AND '"
                  . $requestedDate
                  . "' <= calendar_end)";
        $result = $this->db->query($sql);
        if ($result) {
            list($count) = $this->db->fetchRow($result);
        } else {
            $count = 0;
        }
        /*
                $criteriaCompo = new \CriteriaCompo();
                $criteriaCompo->add(new \Criteria('calendar_status', CALENDAR_STATUS_HOLIDAY, '='));
                $criteriaCompo->add(new \Criteria('calendar_employees_id', $employees_id, '='));
                $criteriaCompo->add(new \Criteria('calendar_start', $requestedDate, '<='));
                $criteriaCompo->add(new \Criteria("'".$requestedDate."'", 'calendar_end', '<='));
                $count = 0;
                $count = $hMsCalendar->getCount($criteriaCompo);
                unset($criteriaCompo);
        */

        if ($count > 0) {
            return true;
        }

        // 2) Recherche de la période où l'heure de début + la durée tombe pendant les vacances
        $hours        = (int)mb_substr($startingHour, 0, 2);
        $minutes      = (int)mb_substr($startingHour, 3, 2);
        $seconds      = (int)mb_substr($startingHour, 6, 2);
        $timestamp    = mktime($hours, $minutes, $seconds, $month, $day, $year) + ($duration * 3600);
        $calculedDate = date('Y-m-d H:i:s', $timestamp);

        $criteriaCompo = new \CriteriaCompo();
        $criteriaCompo->add(new \Criteria('calendar_status', CALENDAR_STATUS_HOLIDAY, '='));
        $criteriaCompo->add(new \Criteria('calendar_employees_id', $employees_id, '='));
        $criteriaCompo->add(new \Criteria('calendar_start', $calculedDate, '>='));
        $criteriaCompo->add(new \Criteria('calendar_end', $calculedDate, '<='));
        $count = 0;
        $count = $hMsCalendar->getCount($criteriaCompo);
        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * Indique si une personne est déjà au travail pour une date donnée
     *
     * @param int          $year         Année
     * @param  int         $month        Mois
     * @param  int         $day          Jour
     * @param string|float $startingHour Heure de début
     * @param int          $duration     Durée en heures
     * @param int          $employees_id Identifiant de l'employé(e)
     * @return bool Vrai si la personne est déjà au travail (ou si elle n'est plus au travail mais elle n'a pas le temps de battement)
     */
    public function isEmployeeWorking($year, $month, $day, $startingHour, $duration, $employees_id)
    {
        global $hMsCalendar;
        $battement          = \XoopsModules\Myservices\Utilities::getModuleOption('battement') * 60;    // Conversion en secondes
        $startingHour       = \XoopsModules\Myservices\Utilities::normalyzeTime($startingHour);
        $requestedDate      = sprintf('%04d-%02d-%02d %s', $year, $month, $day, $startingHour);
        $shortRequestedDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
        // 1) Recherche du "en plein dedans" (L'heure demandée tombe dans un créneau horaire où la personne travaille déjà)
        $sql    = 'SELECT COUNT(*) AS cpt FROM ' . $this->db->prefix('myservices_calendar') . ' WHERE calendar_status = ' . CALENDAR_STATUS_WORK . ' AND calendar_employees_id = ' . (int)$employees_id . " AND (calendar_start <= '" . $requestedDate . "' AND '" . $requestedDate . "' <= calendar_end)";
        $result = $this->db->query($sql);
        if ($result) {
            list($count) = $this->db->fetchRow($result);
        } else {
            $count = 0;
        }

        /*
                $criteriaCompo = new \CriteriaCompo();
                $criteriaCompo->add(new \Criteria('calendar_status', CALENDAR_STATUS_WORK, '='));
                $criteriaCompo->add(new \Criteria('calendar_employees_id', $employees_id, '='));
                $criteriaCompo->add(new \Criteria('calendar_start', $requestedDate, '>='));
                $criteriaCompo->add(new \Criteria('calendar_end', $requestedDate, '<='));
                $count = 0;
                $count = $hMsCalendar->getCount($criteriaCompo);
                unset($criteriaCompo);
        */
        if ($count > 0) {
            return true;
        }

        // 2) Recherche de la période où l'heure de début + la durée tombe pendant une autre période
        $hours        = (int)mb_substr($startingHour, 0, 2);
        $minutes      = (int)mb_substr($startingHour, 3, 2);
        $seconds      = (int)mb_substr($startingHour, 6, 2);
        $timestamp    = mktime($hours, $minutes, $seconds, $month, $day, $year);    // timestamp de la date et heure demand�e
        $calculedDate = date('Y-m-d H:i:s', $timestamp + ($duration * 3600));

        $criteriaCompo = new \CriteriaCompo();
        $criteriaCompo->add(new \Criteria('calendar_status', CALENDAR_STATUS_WORK, '='));
        $criteriaCompo->add(new \Criteria('calendar_employees_id', $employees_id, '='));
        $criteriaCompo->add(new \Criteria('calendar_start', $calculedDate, '>='));
        $criteriaCompo->add(new \Criteria('calendar_end', $calculedDate, '<='));
        $count = 0;
        $count = $hMsCalendar->getCount($criteriaCompo);
        if ($count > 0) {
            return true;
        }
        // 3) Vérifications par rapport au temps de battement et aux réservations déjà faites ce même jour
        unset($criteriaCompo);
        $criteriaCompo = new \CriteriaCompo();
        $criteriaCompo->add(new \Criteria('calendar_status', CALENDAR_STATUS_WORK, '='));
        $criteriaCompo->add(new \Criteria('calendar_employees_id', $employees_id, '='));
        $criteriaCompo->add(new \Criteria('date(calendar_start)', $shortRequestedDate, '='));
        $reservations = [];
        $reservations = $hMsCalendar->getObjects($criteriaCompo);
        foreach ($reservations as $reservation) {
            $startingHour = strtotime($reservation->getVar('calendar_start')) - $battement;
            $endingHour   = strtotime($reservation->getVar('calendar_end')) + $battement;
            if ($timestamp > $startingHour || $timestamp < $endingHour) {
                return true;
            }
        }

        return false;
    }

    /**
     * Indique si une personne est disponible pour un service, pour une date et pour une durée
     * @param int    $year         L'année
     * @param  int   $month        Le mois
     * @param  int   $day          Le jour
     * @param string $hour         L'heure de début de réservation (au format 17:10)
     * @param int    $duration     La durée de la réservation
     * @param  int   $products_id  L'identifiant du produit
     * @param int    $employees_id L'identifiant du salarié
     * @return bool Vrai si la personne est disponible sinon faux
     */
    public function isEmployeeAvailable($year, $month, $day, $hour, $duration, $products_id, $employees_id)
    {
        global $hMsProducts, $hMsCalendar, $hMsPrefs;
        $product = $employee = null;
        // Récupération du produit
        $product = $hMsProducts->get($products_id);
        if (!is_object($product)) {
            //file_put_contents('verif.txt',' produit introuvable');
            return false;
        }
        $employee = $this->get($employees_id);
        if (!is_object($employee)) {
            //file_put_contents('verif.txt',' employé introuvable');
            return false;
        }

        // 1) Est ce que la personne est active ?
        if (1 != $employee->getVar('employees_isactive')) {
            //file_put_contents('verif.txt',' employé inactif');
            return false;
        }

        $hour = \XoopsModules\Myservices\Utilities::normalyzeTime($hour);

        // 2) La personne est en vacances ?
        if ($this->isEmployeeInHoliday($year, $month, $day, $hour, $duration, $employees_id)) {
            //file_put_contents('verif.txt',' employé en vacances');
            return false;
        }
        // 3) Travaille déjà ?
        if ($this->isEmployeeWorking($year, $month, $day, $hour, $duration, $employees_id)) {
            //file_put_contents('verif.txt',' travaille déjà');
            return false;
        }

        // 4) Heure de début + durée = en dehors des heures de travail ?
        $hours           = (int)mb_substr($hour, 0, 2);
        $minutes         = (int)mb_substr($hour, 3, 2);
        $seconds         = (int)mb_substr($hour, 6, 2);
        $timestamp       = mktime($hours, $minutes, $seconds, $month, $day, $year) + ($duration * 3600);
        $calculdatedHour = date('H:i:s', $timestamp);
        if (!$hMsPrefs->isStoreOpen($year, $month, $day, $calculdatedHour)) {
            //file_put_contents('verif.txt',' magasin pas ouvert');
            return false;
        }

        // 5) Toute la durée de la prestation tombe pendant les heures de travail ?
        $hours     = (int)mb_substr($hour, 0, 2);
        $minutes   = (int)mb_substr($hour, 3, 2);
        $seconds   = (int)mb_substr($hour, 6, 2);
        $timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
        for ($i = 1; $i <= $duration; ++$i) {
            $newDate      = getdate($timestamp);
            $formatedHour = sprintf('%02d:%02d:%02d', $newDate['hours'], $newDate['minutes'], $newDate['seconds']);
            if (!$hMsPrefs->isStoreOpen($newDate['year'], $newDate['mon'], $newDate['mday'], $formatedHour, true)) {
                //file_put_contents('verif.txt'," Une heure tombe pendant les heures de fermeture du magasin ".$formatedHour);
                return false;
            }
            $timestamp += 3600;
        }

        return true;
    }
}
