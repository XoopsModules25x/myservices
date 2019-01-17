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

define('MYSERVICES_ORDER_NOINFORMATION', 0);    // Pas encore d'informations sur la commande
define('MYSERVICES_ORDER_VALIDATED', 1);        //  Commande validée par Paypal
define('MYSERVICES_ORDER_PENDING', 2);            // En attente
define('MYSERVICES_ORDER_FAILED', 3);            // Echec
define('MYSERVICES_ORDER_CANCELED', 4);            // Annulée
define('MYSERVICES_ORDER_FRAUD', 5);            // Fraude

/**
 * Class Orders
 * @package XoopsModules\Myservices
 */
class Orders extends Myservices\ServiceObject
{
    public function __construct()
    {
        $this->initVar('orders_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('orders_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('orders_date', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_state', XOBJ_DTYPE_INT, null, false);
        $this->initVar('orders_ip', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_firstname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_lastname', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_address', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('orders_zip', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_town', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_country', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_telephone', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_email', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_articles_count', XOBJ_DTYPE_INT, null, false);
        $this->initVar('orders_total', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_password', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('orders_cancel', XOBJ_DTYPE_TXTBOX, null, false);
    }
}
