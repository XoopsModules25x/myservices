<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

/**
 * Affiche la liste de tous les employés actifs
 */
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'myservices_employees.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$tblEmployees = [];
$tblEmployees = $hMsEmployees->getActiveEmployees();
foreach ($tblEmployees as $item) {
    $xoopsTpl->append('employees', $item->toArray());
}

$xoopsTpl->assign('moduleName', \XoopsModules\Myservices\Utilities::getModuleName());

// Titre de page et meta description ****************************************************
$pageTitle = _MYSERVICES_EMPLOYEES_LIST . ' - ' . \XoopsModules\Myservices\Utilities::getModuleName();
\XoopsModules\Myservices\Utilities::setMetas($pageTitle, $pageTitle);
require_once XOOPS_ROOT_PATH . '/footer.php';
