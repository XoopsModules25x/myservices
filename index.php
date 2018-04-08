<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

/**
 * Liste des catégories mères du module
 */
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'myservices_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

require_once XOOPS_ROOT_PATH . '/class/template.php';

$myTpl = new \XoopsTpl();
// Module Preferences
$xoopsTpl->assign('columnsCount', myservices_utils::getModuleOption('columnscount'));

// Lecture de toutes les TVA ************************************************************
$vatArray = [];
$vatArray = $hMsVat->getItems();

// Recherche des données
$categories = $datas = [];
$categories = $hMsCategories->getMotherCategories();
foreach ($categories as $category) {
    $myTpl->assign('category', $category->toArray());
    $datas[] = $myTpl->fetch('db:myservices_onecategory.tpl', $category->toArray());
    $myTpl->clear_all_assign();
}
$xoopsTpl->assign('categories', $datas);
unset($myTpl);

// Titre de page
myservices_utils::setMetas(_MYSERVICES_CATEGORIES, _MYSERVICES_CATEGORIES);
require_once XOOPS_ROOT_PATH . '/footer.php';
