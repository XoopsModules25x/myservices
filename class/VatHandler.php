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

/**
 * Class VatHandler
 * @package XoopsModules\Myservices
 */
class VatHandler extends Myservices\ServiceORM
{
    /**
     * VatHandler constructor.
     * @param $db
     */
    public function __construct($db)
    {    //                         Table               Classe          Id      Description
        parent::__construct($db, 'myservices_vat', Vat::class, 'vat_id', 'vat_rate');
    }
}
