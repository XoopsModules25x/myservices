<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

/**
 * Affiche les informations d'un salarié
 */
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'myservices_employe.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$employees_id = \Xmf\Request::getInt('employees_id', 0, 'GET');
if (0 == $employees_id) {
    \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR8, 'index.php', 5);
}

// La personne existe ?
$employee = null;
$employee = $hMsEmployees->get($employees_id);
if (!is_object($employee)) {
    \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR11, 'index.php', 5);
}

// La personne est toujours active ?
if (0 == $employee->getVar('employees_isactive')) {
    \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR12, 'index.php', 5);
}
$xoopsTpl->assign('employee', $employee->toArray());
// Recherche des services fournis par cette personne
$listProduits = [];
$listProduits = $hMsEmployees->getServicesFromEmployee($employees_id);
foreach ($listProduits as $item) {
    $xoopsTpl->append('products', $item->toArray());
}

$xoopsTpl->assign('moduleName', \XoopsModules\Myservices\Utilities::getModuleName());

// Titre de page et meta description ****************************************************
$pageTitle = $employee->getVar('employees_lastname') . ' ' . $employee->getVar('employees_firstname') . ' - ' . \XoopsModules\Myservices\Utilities::getModuleName();
\XoopsModules\Myservices\Utilities::setMetas($pageTitle, $pageTitle);
require_once XOOPS_ROOT_PATH . '/footer.php';
