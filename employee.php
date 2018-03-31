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
$xoopsOption['template_main'] = 'myservices_employe.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$employes_id = \Xmf\Request::getInt('employes_id', 0, 'GET');
if (0 == $employes_id) {
    myservices_utils::redirect(_MYSERVICES_ERROR8, 'index.php', 5);
}

// La personne existe ?
$employee = null;
$employee = $hMsEmployes->get($employes_id);
if (!is_object($employee)) {
    myservices_utils::redirect(_MYSERVICES_ERROR11, 'index.php', 5);
}

// La personne est toujours active ?
if (0 == $employee->getVar('employes_isactive')) {
    myservices_utils::redirect(_MYSERVICES_ERROR12, 'index.php', 5);
}
$xoopsTpl->assign('employee', $employee->toArray());
// Recherche des services fournis par cette personne
$listProduits = [];
$listProduits = $hMsEmployes->getServicesFromEmployee($employes_id);
foreach ($listProduits as $item) {
    $xoopsTpl->append('products', $item->toArray());
}

$xoopsTpl->assign('moduleName', myservices_utils::getModuleName());

// Titre de page et meta description ****************************************************
$pageTitle = $employee->getVar('employes_lastname') . ' ' . $employee->getVar('employes_firstname') . ' - ' . myservices_utils::getModuleName();
myservices_utils::setMetas($pageTitle, $pageTitle);
require_once XOOPS_ROOT_PATH . '/footer.php';
