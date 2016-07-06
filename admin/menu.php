<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */
defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

//$path = dirname(dirname(dirname(__DIR__)));
//include_once $path . '/mainfile.php';

$moduleDirName = basename(dirname(__DIR__));

$moduleHandler = xoops_getHandler('module');
$module        = $moduleHandler->getByDirname($moduleDirName);
$pathIcon32    = '../../' . $module->getInfo('sysicons32');
$pathModIcon32 = './' . $module->getInfo('modicons32');
xoops_loadLanguage('modinfo', $module->dirname());

$xoopsModuleAdminPath = XOOPS_ROOT_PATH . '/' . $module->getInfo('dirmoduleadmin');
if (!file_exists($fileinc = $xoopsModuleAdminPath . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/' . 'main.php')) {
    $fileinc = $xoopsModuleAdminPath . '/language/english/main.php';
}
include_once $fileinc;

$adminmenu[] = array(
    'title' => _AM_MODULEADMIN_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png'
);

$adminmenu[] = array(
    'title' => _MI_MYSERVICES_ADMENU0,
    'link'  => 'admin/main.php?op=dashboard',
    'icon'  => $pathIcon32 . '/manage.png'
);

$adminmenu[] = array(
    'title' => _MI_MYSERVICES_ADMENU1,
    'link'  => 'admin/main.php?op=vat',
    'icon'  => $pathIcon32 . '/calculator.png'
);

$adminmenu[] = array(
    'title' => _MI_MYSERVICES_ADMENU2,
    'link'  => 'admin/main.php?op=employes',
    'icon'  => $pathIcon32 . '/users.png'
);

$adminmenu[] = array(
    'title' => _MI_MYSERVICES_ADMENU3,
    'link'  => 'admin/main.php?op=holiday',
    'icon'  => $pathIcon32 . '/face-smile.png'
);

$adminmenu[] = array(
    'title' => _MI_MYSERVICES_ADMENU4,
    'link'  => 'admin/main.php?op=categories',
    'icon'  => $pathIcon32 . '/category.png'
);

$adminmenu[] = array(
    'title' => _MI_MYSERVICES_ADMENU5,
    'link'  => 'admin/main.php?op=products',
    'icon'  => $pathIcon32 . '/delivery.png'
);

//$adminmenu[] = array(
//    'title' => _MI_MYSERVICES_ADMENU6,
//    'link'  => 'admin/main.php',
//    'icon'  => $pathIcon32.'/manage.png'
//);

$adminmenu[] = array(
    'title' => _MI_MYSERVICES_ADMENU7,
    'link'  => 'admin/main.php?op=orders',
    'icon'  => $pathIcon32 . '/cart_add.png'
);

$adminmenu[] = array(
    'title' => _MI_MYSERVICES_ADMENU8,
    'link'  => 'admin/main.php?op=texts',
    'icon'  => $pathIcon32 . '/highlight.png'
);

$adminmenu[] = array(
    'title' => _MI_MYSERVICES_ADMENU9,
    'link'  => 'admin/main.php?op=timesheet',
    'icon'  => $pathIcon32 . '/event.png'
);

$adminmenu[] = array(
    'title' => _AM_MODULEADMIN_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png'
);
