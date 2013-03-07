<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id$
 * ****************************************************************************
 */

if (!defined('XOOPS_ROOT_PATH')) {
	die('XOOPS root path not defined');
}

/**
 * Liste des catégories
 */
function b_ms_detcategories_show()
{
	require XOOPS_ROOT_PATH.'/modules/myservices/include/common.php';
	require_once XOOPS_ROOT_PATH.'/class/template.php';
	$block = array();

	$myTpl = new XoopsTpl();
	// Préférences du module
	$block['blockColumnsCount'] = myservices_utils::getModuleOption('columnscount');

	// Lecture de toutes les TVA ************************************************************
	$vatArray = array();
	$vatArray = $hMsVat->getItems();

	// Recherche des données
	$categories = $datas = array();
	$criteria = new Criteria('categories_pid', 0, '=');
	$criteria->setSort('categories_title');
	$categories = $hMsCategories->getObjects($criteria);
	foreach($categories as $category) {
		$myTpl->assign('category', $category->toArray());
		$datas[] = $myTpl->fetch('db:myservices_onecategory.html', $category->toArray());
		$myTpl->clear_all_assign();
	}
	$block['blockCategories'] = $datas;
	unset($myTpl);
	return $block;
}


/**
 * Bloc à la volée
 */
function b_ms_detcategories_duplicatable($options)
{
	$options = explode('|',$options);
	$block = & b_ms_detcategories_show($options);
	$tpl = new XoopsTpl();
	$tpl->assign('block', $block);
	$tpl->display('db:myservices_block_detcategories.html');
}
?>