<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id$
 * ****************************************************************************
 */

if (!defined('XOOPS_ROOT_PATH')) {
	die("XOOPS root path not defined");
}

require XOOPS_ROOT_PATH.'/class/xoopsobject.php';
if (!class_exists('myservices_ORM')) {
	require XOOPS_ROOT_PATH.'/modules/myservices/class/PersistableObjectHandler.php';
}

class myservices_vat extends myservices_Object
{
	function __construct()
	{
		$this->initVar('vat_id',XOBJ_DTYPE_INT,null,false);
		$this->initVar('vat_rate',XOBJ_DTYPE_TXTBOX,null,false);
	}
}


class MyservicesMyservices_vatHandler extends myservices_ORM
{
	function __construct($db)
	{	//							Table				Classe			Id		Description
		parent::__construct($db, 'myservices_vat', 'myservices_vat', 'vat_id', 'vat_rate');
	}
}
?>