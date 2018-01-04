<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

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
require_once MYSERVICES_PATH . 'class/myservices_utils.php';
require_once MYSERVICES_PATH . 'class/myservices_currency.php';
require_once MYSERVICES_PATH . 'class/PEAR.php';

// Handlers des tables
$hMsCaddy            = xoops_getModuleHandler('myservices_caddy', MYSERVICES_DIRNAME);
$hMsCalendar         = xoops_getModuleHandler('myservices_calendar', MYSERVICES_DIRNAME);
$hMsCategories       = xoops_getModuleHandler('myservices_categories', MYSERVICES_DIRNAME);
$hMsEmployes         = xoops_getModuleHandler('myservices_employes', MYSERVICES_DIRNAME);
$hMsEmployesproducts = xoops_getModuleHandler('myservices_employesproducts', MYSERVICES_DIRNAME);
$hMsOrders           = xoops_getModuleHandler('myservices_orders', MYSERVICES_DIRNAME);
$hMsProducts         = xoops_getModuleHandler('myservices_products', MYSERVICES_DIRNAME);
$hMsVat              = xoops_getModuleHandler('myservices_vat', MYSERVICES_DIRNAME);
$hMsPrefs            = xoops_getModuleHandler('myservices_prefs', MYSERVICES_DIRNAME);

// Definition des images
if (!defined('_MYSERVICES_EDIT')) {
    if (isset($xoopsConfig) && file_exists(MYSERVICES_PATH . 'language/' . $xoopsConfig['language'] . '/main.php')) {
        require MYSERVICES_PATH . 'language/' . $xoopsConfig['language'] . '/main.php';
    } else {
        require MYSERVICES_PATH . 'language/english/main.php';
    }
    // xoops_loadLanguage('main', basename(dirname(__DIR__)));

    $icones = [
        'edit'       => "<img src='" . MYSERVICES_IMAGES_URL . "edit.png' alt='" . _MYSERVICES_EDIT . "' align='middle'>",
        'copy'       => "<img src='" . MYSERVICES_IMAGES_URL . "duplicate.png' alt='" . _MYSERVICES_DUPLICATE_PRODUCT . "' align='middle'>",
        'delete'     => "<img src='" . MYSERVICES_IMAGES_URL . "delete.png' alt='" . _MYSERVICES_DELETE . "' align='middle'>",
        'validate'   => "<img src='" . MYSERVICES_IMAGES_URL . "ok.png' alt='" . _MYSERVICES_BTN_VALIDATE . "' align='middle'>",
        'details'    => "<img src='" . MYSERVICES_IMAGES_URL . "details.png' alt='" . _MYSERVICES_DETAILS . "' align='middle'>",
        'unvalidate' => "<img src='" . MYSERVICES_IMAGES_URL . "button_cancel.png' alt='" . _MYSERVICES_BTN_UNVALIDATE . "' align='middle'>"
    ];
}
