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
 * Bloc, Liste des produits
 */
function b_ms_products_show($options)
{
	// Options = Nombre d'éléments visibles simultanément dans la liste
	require XOOPS_ROOT_PATH.'/modules/myservices/include/common.php';
	require_once MYSERVICES_PATH.'class/tree.php';
	$block = array();
	$itemsCount = intval($options[0]);
	$tblItems = array();
	$select = '';
	$tblItems = $hMsCategories->getItems();
	$mytree = new myservices_XoopsObjectTree($tblItems, 'categories_id', 'categories_pid');
	$jump = MYSERVICES_URL.'product.php?products_id=';
	$additional = " onchange='location=\"".$jump."\"+this.options[this.selectedIndex].value'";
	$additional .= " style='width: 170px; max-width: 170px;'";

	$select .= "<select name='blockSelectProduct' id='blockSelectProduct' size='".$itemsCount."'".$additional.'>';

	$productsPerCategory = $hMsProducts->getProductsPerCategory();
	$tree = $mytree->getAllChild(0);
	foreach($tree as $key => $category) {
		$select .= "<optgroup label=\"".myservices_utils::makeHrefTitle($category->getVar('categories_title')).'">';
		if(isset($productsPerCategory[$category->getVar('categories_id')])) {
			$products = array();
			$products = $productsPerCategory[$category->getVar('categories_id')];
			foreach($products as $product) {
				$select .= "<option value='".$product->getVar('products_id')."'>".$product->getVar('products_title')."</option>";
			}
		}
		$select .= '</optgroup>';
	}
	$select .= '</select>';

	$block['block_select'] = $select;
	return $block;
}

function b_ms_products_edit($options)
{
	// Options = Nombre d'éléments visibles simultanément dans la liste
	$form ='';
	$form .= _MB_MYSERVICES_NBELTS_INLIST." <input type='text' name='options[]' value='".$options[0]."' /><br />";
	return $form;
}

/**
 * Bloc à la volée
 */
function b_ms_products_duplicatable($options)
{
	$options = explode('|',$options);
	$block = & b_ms_products_show($options);
	$tpl = new XoopsTpl();
	$tpl->assign('block', $block);
	$tpl->display('db:myservices_block_products.html');
}
?>