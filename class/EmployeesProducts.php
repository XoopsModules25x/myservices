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
 * Class EmployeesProducts
 * @package XoopsModules\Myservices
 */
class EmployeesProducts extends Myservices\ServiceObject
{
    public function __construct()
    {
        $this->initVar('employeesproducts_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('employeesproducts_employees_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('employeesproducts_products_id', XOBJ_DTYPE_INT, null, false);
    }
}
