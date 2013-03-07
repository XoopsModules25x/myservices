<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */


/**
 * Liste des produits d'une catégorie ainsi que de ses sous-catégrories
 */
require_once 'header.php';
$xoopsOption['template_main'] = 'myservices_category.html';
require_once XOOPS_ROOT_PATH.'/header.php';
require_once XOOPS_ROOT_PATH.'/class/pagenav.php';


// Catégorie sélectionnée ***************************************************************
$currentCategoryId = isset($_GET['categories_id']) ? intval($_GET['categories_id']) : 0;
if( $currentCategoryId == 0 ) {
	myservices_utils::redirect(_MYSERVICES_ERROR1, 'index.php', 5);
}

// Chargement de la catégorie ***********************************************************
$currentCategory = null;
$currentCategory = $hMsCategories->get($currentCategoryId);
if(!is_object($currentCategory)) {
	myservices_utils::redirect(_MYSERVICES_ERROR2, 'index.php', 5);
}
// Formatage de la catégorie courante ***************************************************
$xoopsTpl->assign('category', $currentCategory->toArray());


// Préférences du module ****************************************************************
$xoopsTpl->assign('columnsCount', myservices_utils::getModuleOption('columnscount'));
$xoopsTpl->assign('ProductsPerColumn', myservices_utils::getModuleOption('prodperline'));

$limit = myservices_utils::getModuleOption('perpage');
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;

// Lecture de toutes les TVA ************************************************************
$vatArray = array();
$vatArray = $hMsVat->getItems();

// Construction de la liste des sous-catégories de la catégorie courante ****************
$cpt = 1;
$tblChilds = array();
$tblChilds = $hMsCategories->getAllChild($currentCategoryId);
foreach($tblChilds as $item) {
	$datas = array();
	$datas = $item->toArray();
	$datas['count'] = $cpt;
	$xoopsTpl->append('subCategories', $datas);
	$cpt++;
}

// Breadcrumb ***************************************************************************
$xoopsTpl->assign('breadcrumb', $hMsCategories->getBreadCrumb($currentCategory));

// Recherche des produits de la catégorie courante **************************************
$products = array();

// Comptage du nombre total de produits en ligne dans cette catégorie
$itemsCount = $hMsProducts->getProductsCountFromCategory($currentCategoryId);
if($itemsCount > $limit) {
	$pagenav = new XoopsPageNav($itemsCount, $limit, $start, 'start');
	$xoopsTpl->assign('pagenav', $pagenav->renderNav());
}

$cpt = 1;
$products = $hMsProducts->getProductsFromCategory($currentCategoryId, $start, $limit);
foreach($products as $product) {
	$datas = array();
	$datas = $product->toArray();
	$datas['count'] = $cpt;
	$xoopsTpl->append('products', $datas);
	$cpt++;
}

// Titre de page et meta description
$pageTitle = _MYSERVICES_PRODUCT_CATEGORY.' '.$currentCategory->getVar('categories_title').' - '.myservices_utils::getModuleName();
$metaKeywords = myservices_utils::createMetaKeywords($currentCategory->getVar('categories_title', 'e').' '.$currentCategory->getVar('categories_description', 'e'));
myservices_utils::setMetas($pageTitle, $pageTitle, $metaKeywords);
require_once XOOPS_ROOT_PATH.'/footer.php';
?>