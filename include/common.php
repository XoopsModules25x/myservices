<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id$
 * ****************************************************************************
 */

if (!defined("XOOPS_ROOT_PATH")) {
 	die("XOOPS root path not defined");
}

if( !defined("MYSERVICES_DIRNAME") ) {
	define("MYSERVICES_DIRNAME", 'myservices');
	define("MYSERVICES_TEXTFILE1", 'myservices_cancel.txt');
	define("MYSERVICES_TEXTFILE2", 'myservices_notinhours.txt');
	define("MYSERVICES_TEXTFILE3", 'myservices_cgv.txt');
	define("MYSERVICES_TEXTFILE4", 'myservices_quality.txt');
	define("MYSERVICES_URL", XOOPS_URL.'/modules/'.MYSERVICES_DIRNAME.'/');
	define("MYSERVICES_PATH", XOOPS_ROOT_PATH.'/modules/'.MYSERVICES_DIRNAME.'/');
	define("MYSERVICES_IMAGES_URL", MYSERVICES_URL.'images/');
}
$myts = &MyTextSanitizer::getInstance();

// Chargement des handler et des autres classes
require_once MYSERVICES_PATH.'class/myservices_utils.php';
require_once MYSERVICES_PATH.'class/myservices_currency.php';
require_once MYSERVICES_PATH.'class/PEAR.php';

// Handlers des tables
$hMsCaddy = xoops_getmodulehandler('myservices_caddy', MYSERVICES_DIRNAME);
$hMsCalendar = xoops_getmodulehandler('myservices_calendar', MYSERVICES_DIRNAME);
$hMsCategories = xoops_getmodulehandler('myservices_categories', MYSERVICES_DIRNAME);
$hMsEmployes = xoops_getmodulehandler('myservices_employes', MYSERVICES_DIRNAME);
$hMsEmployesproducts = xoops_getmodulehandler('myservices_employesproducts', MYSERVICES_DIRNAME);
$hMsOrders = xoops_getmodulehandler('myservices_orders', MYSERVICES_DIRNAME);
$hMsProducts = xoops_getmodulehandler('myservices_products', MYSERVICES_DIRNAME);
$hMsVat = xoops_getmodulehandler('myservices_vat', MYSERVICES_DIRNAME);
$hMsPrefs = xoops_getmodulehandler('myservices_prefs', MYSERVICES_DIRNAME);

// Définition des images
if( !defined("_MYSERVICES_EDIT")) {
	if (isset($xoopsConfig) && file_exists(MYSERVICES_PATH.'language/'.$xoopsConfig['language'].'/main.php')) {
			require MYSERVICES_PATH.'language/'.$xoopsConfig['language'].'/main.php';
	} else {
		require MYSERVICES_PATH.'language/english/main.php';
	}

	$icones = array(
		'edit' => "<img src='". MYSERVICES_IMAGES_URL ."edit.png' alt='" . _MYSERVICES_EDIT . "' align='middle' />",
		'copy' => "<img src='". MYSERVICES_IMAGES_URL  ."duplicate.png' alt='" . _MYSERVICES_DUPLICATE_PRODUCT . "' align='middle' />",
		'delete' => "<img src='". MYSERVICES_IMAGES_URL ."delete.png' alt='" . _MYSERVICES_DELETE . "' align='middle' />",
		'validate' => "<img src='". MYSERVICES_IMAGES_URL ."ok.png' alt='" . _MYSERVICES_BTN_VALIDATE . "' align='middle' />",
		'details' => "<img src='". MYSERVICES_IMAGES_URL ."details.png' alt='"._MYSERVICES_DETAILS."' align='middle' />",
		'unvalidate' => "<img src='". MYSERVICES_IMAGES_URL ."button_cancel.png' alt='" . _MYSERVICES_BTN_UNVALIDATE . "' align='middle' />"
	);
}
?>