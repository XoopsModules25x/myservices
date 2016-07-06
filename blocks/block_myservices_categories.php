<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * List of the categories
 * @param $options
 * @return array
 */
function b_ms_categories_show($options)
{
	// Options = Nombre d'éléments visibles simultanément dans la liste
    require XOOPS_ROOT_PATH . '/modules/myservices/include/common.php';
    require_once MYSERVICES_PATH . 'class/tree.php';
    $block      = array();
    $itemsCount = (int)$options[0];
    $tblItems   = array();
    $tblItems   = $hMsCategories->getItems();
    $mytree     = new myservices_XoopsObjectTree($tblItems, 'categories_id', 'categories_pid');

    $jump       = MYSERVICES_URL . 'category.php?categories_id=';
    $additional = "size='" . $itemsCount . "' onchange='location=\"" . $jump . "\"+this.options[this.selectedIndex].value'";
    $additional .= " style='width: 170px; max-width: 170px;'";
    $selectCateg           = $mytree->makeSelBox('blockSelectCateg', 'categories_title', '-', 0, '', 0, $additional);
    $block['block_select'] = $selectCateg;

    return $block;
}

function b_ms_categories_edit($options)
{
    // Options = Count of visible elements
    $form = '';
    $form .= _MB_MYSERVICES_NBELTS_INLIST . " <input type='text' name='options[]' value='" . $options[0] . "' /><br>";

    return $form;
}

/**
 * On the fly block
 * @param $options
 */
function b_ms_categories_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = &b_ms_categories_show($options);
    $tpl     = new XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:myservices_block_categories.tpl');
}
