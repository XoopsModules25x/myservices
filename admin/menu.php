<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

// require_once __DIR__ . '/../class/Helper.php';
//require_once __DIR__ . '/../include/common.php';
$helper = Myservices\Helper::getInstance();

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$adminmenu[] = [
    'title' => _MI_MYSERVICES_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png'
];

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ADMENU0,
    'link'  => 'admin/main.php?op=dashboard',
    'icon'  => $pathIcon32 . '/manage.png'
];

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ADMENU1,
    'link'  => 'admin/main.php?op=vat',
    'icon'  => $pathIcon32 . '/calculator.png'
];

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ADMENU2,
    'link'  => 'admin/main.php?op=employes',
    'icon'  => $pathIcon32 . '/users.png'
];

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ADMENU3,
    'link'  => 'admin/main.php?op=holiday',
    'icon'  => $pathIcon32 . '/face-smile.png'
];

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ADMENU4,
    'link'  => 'admin/main.php?op=categories',
    'icon'  => $pathIcon32 . '/category.png'
];

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ADMENU5,
    'link'  => 'admin/main.php?op=products',
    'icon'  => $pathIcon32 . '/delivery.png'
];

//$adminmenu[] = array(
//    'title' => _MI_MYSERVICES_ADMENU6,
//    'link'  => 'admin/main.php',
//    'icon'  => $pathIcon32.'/manage.png'
//);

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ADMENU7,
    'link'  => 'admin/main.php?op=orders',
    'icon'  => $pathIcon32 . '/cart_add.png'
];

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ADMENU8,
    'link'  => 'admin/main.php?op=texts',
    'icon'  => $pathIcon32 . '/highlight.png'
];

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ADMENU9,
    'link'  => 'admin/main.php?op=timesheet',
    'icon'  => $pathIcon32 . '/event.png'
];

$adminmenu[] = [
    'title' => _MI_MYSERVICES_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png'
];
