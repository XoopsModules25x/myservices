<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 24 nov. 07 at 14:33:15
 * ****************************************************************************
 */

/**
 * Affiche la liste de tous les produits
 */
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'myservices_products.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$tblProducts = $tblProductsForDisp = $tblCategories = [];

$tblCategories = $hMsCategories->getObjects(null, true);
$criteria      = new \Criteria('products_online', 1, '=');
$criteria->setSort('products_categories_id, products_title');
$tblProducts = $hMsProducts->getObjects($criteria);
foreach ($tblProducts as $product) {
    $data                                                             = $product->toArray();
    $data['products_category']                                        = isset($tblCategories[$product->getVar('products_categories_id')]) ? $tblCategories[$product->getVar('products_categories_id')]->toArray() : null;
    $tblProductsForDisp[$product->getVar('products_categories_id')][] = $data;
}
$xoopsTpl->assign('products', $tblProductsForDisp);

// Titre de page et meta description ****************************************************
$pageTitle = _MYSERVICES_LISTE . ' - ' . myservices_utils::getModuleName();
myservices_utils::setMetas($pageTitle, $pageTitle);
require_once XOOPS_ROOT_PATH . '/footer.php';
