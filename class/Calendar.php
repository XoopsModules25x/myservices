<?php namespace XoopsModules\Myservices;

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

define('CALENDAR_STATUS_WORK', 1);        // Au travail
define('CALENDAR_STATUS_HOLIDAY', 2);    // Absent(e)
define('CALENDAR_STATUS_CLOSED', 3);    //  Magasin fermé

/**
 * Class Calendar
 * @package XoopsModules\Myservices
 */
class Calendar extends Myservices\ServiceObject
{
    public function __construct()
    {
        $this->initVar('calendar_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('calendar_status', XOBJ_DTYPE_INT, null, false);
        $this->initVar('calendar_employees_id', XOBJ_DTYPE_INT, null, false);
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
        $tblStatus = [
            CALENDAR_STATUS_WORK    => _MYSERVICES_STATE_WORK,
            CALENDAR_STATUS_HOLIDAY => _MYSERVICES_STATE_HOLIDAY,
            CALENDAR_STATUS_CLOSED  => _MYSERVICES_STATE_CLOSED
        ];
        if (isset($tblStatus[$this->getVar('calendar_status')])) {
            return $tblStatus[$this->getVar('calendar_status')];
        }

        return _MYSERVICES_STATE_UNDEFINED;
    }
}
