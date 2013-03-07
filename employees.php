<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id: employees.php 11 2007-10-24 20:32:10Z hthouzard $
 * ****************************************************************************
 */

 /**
  * Affiche la liste de tous les employés actifs
  */
require_once 'header.php';
$xoopsOption['template_main'] = 'myservices_employes.html';
require_once XOOPS_ROOT_PATH.'/header.php';

$tblEmployes = array();
$tblEmployes = $hMsEmployes->getActiveEmployees();
foreach($tblEmployes as $item) {
	$xoopsTpl->append('employees', $item->toArray());
}

$xoopsTpl->assign('moduleName', myservices_utils::getModuleName());

// Titre de page et meta description ****************************************************
$pageTitle = _MYSERVICES_EMPLOYEES_LIST.' - '.myservices_utils::getModuleName();
myservices_utils::setMetas($pageTitle, $pageTitle);
require_once XOOPS_ROOT_PATH.'/footer.php';
?>