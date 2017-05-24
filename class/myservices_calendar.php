<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

//require XOOPS_ROOT_PATH.'/kernel/object.php';
if (!class_exists('myservices_ORM')) {
    require XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
}

define('CALENDAR_STATUS_WORK', 1);        // Au travail
define('CALENDAR_STATUS_HOLIDAY', 2);    // Absent(e)
define('CALENDAR_STATUS_CLOSED', 3);    //  Magasin fermé

class myservices_calendar extends myservices_Object
{
    public function __construct()
    {
        $this->initVar('calendar_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('calendar_status', XOBJ_DTYPE_INT, null, false);
        $this->initVar('calendar_employes_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('calendar_start', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('calendar_end', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('calendar_products_id', XOBJ_DTYPE_INT, null, false);
    }

    /**
     * Renvoie le libellé correspondant au statut de l'objet courant
     *
     * @return string Le libellé correspondant ou "Non défini"
     */
    public function getStatusLabel()
    {
        $tblStatus = array(
            CALENDAR_STATUS_WORK    => _MYSERVICES_STATE_WORK,
            CALENDAR_STATUS_HOLIDAY => _MYSERVICES_STATE_HOLIDAY,
            CALENDAR_STATUS_CLOSED  => _MYSERVICES_STATE_CLOSED
        );
        if (isset($tblStatus[$this->getVar('calendar_status')])) {
            return $tblStatus[$this->getVar('calendar_status')];
        } else {
            return _MYSERVICES_STATE_UNDEFINED;
        }
    }
}

class MyservicesMyservices_calendarHandler extends myservices_ORM
{
    public function __construct($db)
    {    //                             Table                   Classe              Id
        parent::__construct($db, 'myservices_calendar', 'myservices_calendar', 'calendar_id');
    }

    /**
     * Renvoie les numéros (de 1 à 28,29,30,31) des jours de fermetures exceptionnels du magasin dans un mois
     *
     * @param integer $month Le numéro du mois
     * @param integer $year  L'année
     * @return array en valeur les jours du mois (de 1 à 31) qui sont fermés
     */
    public function getClosedDayInMonth($month, $year)
    {
        $closedDates = $days = array();
        $daysMonth   = date('t', mktime(1, 1, 1, $month, 1, $year));            // Nombre de jours dans le mois
        $startingDay = sprintf('%04d-%02d-01', $year, $month);                // Premier jour du mois
        $endingDay   = sprintf('%04d-%02d-%02d', $year, $month, $daysMonth);    // Dernier jour du mois
        $criteria    = new CriteriaCompo();
        $criteria->add(new Criteria('calendar_status', CALENDAR_STATUS_CLOSED, '='));
        $criteria->add(new Criteria('date(calendar_start)', $startingDay, '>='));
        $criteria->add(new Criteria('date(calendar_start)', $endingDay, '<='));
        //$criteria->add(new Criteria('date(calendar_end)', $endingDay, '<='));
        $closedDates = $this->getObjects($criteria, false, true, 'calendar_start, calendar_end');
        if (count($closedDates) > 0) {
            foreach ($closedDates as $closedDay) {
                $elementsEndDate   = getdate(strtotime($closedDay->getVar('calendar_end')));
                $elementsStartDate = getdate(strtotime($closedDay->getVar('calendar_start')));
                $start             = $elementsStartDate['mday'];

                if ((int)$elementsEndDate['mon'] != (int)$month) {    // Si le mois de la date de fin n'est pas �gal au mois trait� alors
                    $end = $daysMonth;    // On prend le dernier jour du mois
                } else {
                    $end = (int)$elementsEndDate['mday'];    // Sinon on prend le jour sp�cifi� par la date de fin
                }
                for ($i = $start; $i <= $end; ++$i) {
                    $days[] = $i;
                }
            }
        }

        return $days;
    }

    /**
     * Indique si le magasin est exceptionnellement fermé à une date donnée
     *
     * @param integer  $year  Année à de la date
     * @param  integer $month Mois de la date
     * @param  integer $day   Jour de la date
     * @return bool Vrai = magasin fermé, False = magasin ouvert
     */
    public function isStoreClosed($year, $month, $day)
    {
        $startingDay = sprintf('%04d-%02d-01', $year, $month);                // Premier jour du mois
        $endingDay   = $startingDay;

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('calendar_status', CALENDAR_STATUS_CLOSED, '='));
        $criteria->add(new Criteria('date(calendar_start)', $startingDay, '>='));
        $criteria->add(new Criteria('date(calendar_start)', $endingDay, '<='));
        $count = $this->getCount($criteria);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Indique si une personne est disponible pour une date donnée ainsi qu'une tranche horaire donnée
     * @param $year
     * @param $month
     * @param $day
     * @param $hour
     * @param $duration
     */
    public function isEmployeeAvailable($year, $month, $day, $hour, $duration)
    {
    }
}    // Fin des haricots
