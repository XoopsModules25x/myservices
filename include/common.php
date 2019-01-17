<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

include dirname(__DIR__) . '/preloads/autoloader.php';

//defined('XOOPS_ROOT_PATH') || die('Restricted access');

/** @var \XoopsDatabase $db */
/** @var \XoopsModules\Myservices\Helper $helper */
/** @var \XoopsModules\Myservices\Utility $utility */
$db      = \XoopsDatabaseFactory::getDatabaseConnection();
$helper  = \XoopsModules\Myservices\Helper::getInstance();
$utility = new \XoopsModules\Myservices\Utility();

if (!defined('MYSERVICES_DIRNAME')) {
    define('MYSERVICES_DIRNAME', 'myservices');
    define('MYSERVICES_TEXTFILE1', 'myservices_cancel.txt');
    define('MYSERVICES_TEXTFILE2', 'myservices_notinhours.txt');
    define('MYSERVICES_TEXTFILE3', 'myservices_cgv.txt');
    define('MYSERVICES_TEXTFILE4', 'myservices_quality.txt');
    define('MYSERVICES_URL', XOOPS_URL . '/modules/' . MYSERVICES_DIRNAME . '/');
    define('MYSERVICES_PATH', XOOPS_ROOT_PATH . '/modules/' . MYSERVICES_DIRNAME . '/');
    define('MYSERVICES_IMAGES_URL', MYSERVICES_URL . 'assets/images/');
    //    define('MYSERVICES_CACHE_PATH', XOOPS_UPLOAD_PATH . '/' . MYSERVICES_DIRNAME . '/cache');
}
$myts = \MyTextSanitizer::getInstance();

// Chargement des handler et des autres classes
require_once MYSERVICES_PATH . 'class/Utilities.php';
require_once MYSERVICES_PATH . 'class/Currency.php';
require_once MYSERVICES_PATH . 'class/PEAR.php';

// Handlers des tables
$hMsCaddy             = $helper->getHandler('Caddy');
$hMsCalendar          = $helper->getHandler('Calendar');
$hMsCategories        = $helper->getHandler('Categories');
$hMsEmployees         = $helper->getHandler('Employees');
$hMsEmployeesProducts = $helper->getHandler('EmployeesProducts');
$hMsOrders            = $helper->getHandler('Orders');
$hMsProducts          = $helper->getHandler('Products');
$hMsVat               = $helper->getHandler('Vat');
$hMsPrefs             = $helper->getHandler('Preferences');

// Definition des images
if (!defined('_MYSERVICES_EDIT')) {
    /** @var Myservices\Helper $helper */
    $helper = Myservices\Helper::getInstance();
    $helper->loadLanguage('main');

    $icones = [
        'edit'       => "<img src='" . MYSERVICES_IMAGES_URL . "edit.png' alt='" . _MYSERVICES_EDIT . "' align='middle'>",
        'copy'       => "<img src='" . MYSERVICES_IMAGES_URL . "duplicate.png' alt='" . _MYSERVICES_DUPLICATE_PRODUCT . "' align='middle'>",
        'delete'     => "<img src='" . MYSERVICES_IMAGES_URL . "delete.png' alt='" . _MYSERVICES_DELETE . "' align='middle'>",
        'validate'   => "<img src='" . MYSERVICES_IMAGES_URL . "ok.png' alt='" . _MYSERVICES_BTN_VALIDATE . "' align='middle'>",
        'details'    => "<img src='" . MYSERVICES_IMAGES_URL . "details.png' alt='" . _MYSERVICES_DETAILS . "' align='middle'>",
        'unvalidate' => "<img src='" . MYSERVICES_IMAGES_URL . "button_cancel.png' alt='" . _MYSERVICES_BTN_UNVALIDATE . "' align='middle'>",
    ];
}
