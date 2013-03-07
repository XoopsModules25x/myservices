<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id: employee.php 11 2007-10-24 20:32:10Z hthouzard $
 * ****************************************************************************
 */

/**
 * Affiche les informations d'un salarié
 */
require_once 'header.php';
$xoopsOption['template_main'] = 'myservices_employe.html';
require_once XOOPS_ROOT_PATH.'/header.php';

$employes_id = isset($_GET['employes_id']) ? intval($_GET['employes_id']) : 0;
if($employes_id == 0) {
	myservices_utils::redirect(_MYSERVICES_ERROR8, 'index.php', 5);
}

// La personne existe ?
$employee = null;
$employee = $hMsEmployes->get($employes_id);
if(!is_object($employee)) {
	myservices_utils::redirect(_MYSERVICES_ERROR11, 'index.php', 5);
}

// La personne est toujours active ?
if($employee->getVar('employes_isactive') == 0) {
	myservices_utils::redirect(_MYSERVICES_ERROR12, 'index.php', 5);
}
$xoopsTpl->assign('employee', $employee->toArray());
// Recherche des services fournis par cette personne
$listProduits = array();
$listProduits = $hMsEmployes->getServicesFromEmployee($employes_id);
foreach($listProduits as $item) {
	$xoopsTpl->append('products', $item->toArray());
}

$xoopsTpl->assign('moduleName', myservices_utils::getModuleName());

// Titre de page et meta description ****************************************************
$pageTitle = $employee->getVar('employes_lastname').' '.$employee->getVar('employes_firstname').' - '.myservices_utils::getModuleName();
myservices_utils::setMetas($pageTitle, $pageTitle);
require_once XOOPS_ROOT_PATH.'/footer.php';
?>