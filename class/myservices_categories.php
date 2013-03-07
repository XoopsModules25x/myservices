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
	die("XOOPS root path not defined");
}

require XOOPS_ROOT_PATH.'/class/xoopsobject.php';
if (!class_exists('myservices_ORM')) {
	require XOOPS_ROOT_PATH.'/modules/myservices/class/PersistableObjectHandler.php';
}

class myservices_categories extends myservices_Object
{
	function __construct()
	{
		$this->initVar('categories_id',XOBJ_DTYPE_INT,null,false);
		$this->initVar('categories_pid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('categories_title',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('categories_imgurl',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('categories_description',XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('categories_advertisement',XOBJ_DTYPE_TXTAREA, null, false);

		// Pour autoriser le html
		$this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
	}

	/**
	 * Returns the link to use to go to a category according to the module's options (with or without URL rewriting)
	 * @return string the html link to go to the category
	 */
	function getCategoryLink()
	{
		$cat_cid = $this->getVar('categories_id');
		$cat_title = $this->getVar('categories_title', 'n');
		$url = '';

		if( myservices_utils::getModuleOption('urlrewriting') == 1 ) {	// On utilise l'url rewriting
			$url = MYSERVICES_URL.'category-'.intval($cat_cid).myservices_utils::makeSeoUrl($cat_title).'.html';
		} else {	// Pas d'utilisation de l'url rewriting
			$url = MYSERVICES_URL.'category.php?categories_id='.intval($cat_cid);
		}
		return $url;
	}

	/**
	 * Returns data formated
	 *
	 * @param string $format Format de retour des données (en accord avec les paramètres de la fonction getVar() de XoopsObject)
	 * @return array Formated datas
	 */
    function toArray($format = 's')
    {
		$ret = array();
		foreach($this->vars as $k => $v) {
			$ret[$k] = $this->getVar($k, $format);
		}
		$ret['categories_href_title'] = myservices_utils::makeHrefTitle($this->getVar('categories_title'));
		$ret['categories_url'] = $this->getCategoryLink();

		// URL complète de l'image
		if(xoops_trim($this->getVar('categories_imgurl')) != '') {
			$ret['categories_fullimgurl'] = XOOPS_UPLOAD_URL.'/'.$this->getVar('categories_imgurl');
		} else {
			$ret['categories_fullimgurl'] = MYSERVICES_IMAGES_URL.'blank.gif';
		}
		return $ret;
    }
}


class MyservicesMyservices_categoriesHandler extends myservices_ORM
{
	function __construct($db)
	{	//								Table					Classe			 		Id				Description
		parent::__construct($db, 'myservices_categories', 'myservices_categories', 'categories_id', 'categories_title');
	}

	/**
	 * Renvoie la liste des catégories filles d'une catégorie particulière
	 *
	 * @param integer $categories_id Id de la catégorie dont on veut récupérer les filles
	 * @return array Tableau d'objets catégories
	 */
	function getAllChild($categories_id)
	{
		require_once XOOPS_ROOT_PATH.'/class/tree.php';
		$allCategories = $tblChilds = array();
		$allCategories = $this->getItems();
		$mytree = new XoopsObjectTree($allCategories, 'categories_id', 'categories_pid');
		$tblChilds = $mytree->getAllChild($categories_id);
		return $tblChilds;
	}

	/**
	 * Renvoie la liste des catégories mères (les catégories de plus haut niveau qui n'ont pas de mère)
	 *
	 * @return array Objects of type categories
	 */
	function getMotherCategories()
	{
		$tblItems = array();
		$criteria = new Criteria('categories_pid', 0, '=');
		$criteria->setSort('categories_title');
		$tblItems = $this->getObjects($criteria, true);
		return $tblItems;
	}

	/**
	 * Renvoie un breadcrumb jusqu'à une catégorie déterminée
	 *
	 * @param object $currentCategory Objet de type catégorie qui représente la catégorie courante
	 * @param string $raquo Le "séparateur" à utiliser entre les chaines de caractères
	 * @return string Le breadcrumb depuis la page d'index jusqu'à la catégorie courante
	 */
    function getBreadCrumb(myservices_categories $currentCategory, $raquo=' &raquo; ')
    {
    	require_once XOOPS_ROOT_PATH.'/class/tree.php';
    	$currentCategoryId = $currentCategory->getVar('categories_id');
		$allCategories = array();
		$allCategories = $this->getItems();
		$mytree = new XoopsObjectTree($allCategories, 'categories_id', 'categories_pid');

		$tblTmp = $tblAncestors = array();
		$tblAncestors = $mytree->getAllParent($currentCategoryId);
		$tblAncestors = array_reverse($tblAncestors);
		$moduleName = myservices_utils::getModuleName();

		// Ajout de la page d'index avec le nom du module (permet de renvoyer vers la liste des catégories de niveau 1)
		$tblTmp[] = "<a href='".MYSERVICES_URL."index.php' title='".myservices_utils::makeHrefTitle($moduleName)."'>".$moduleName.'</a>';
		foreach($tblAncestors as $item) {
			$tblTmp[] = "<a href='".$item->getCategoryLink()."' title='".myservices_utils::makeHrefTitle($item->getVar('categories_title'))."'>".$item->getVar('categories_title').'</a>';
		}
		// Ajout de la catégorie courante
		$tblTmp[] = "<a href='".$currentCategory->GetCategoryLink()."' title='".myservices_utils::makeHrefTitle($currentCategory->getVar('categories_title'))."'>".$currentCategory->getVar('categories_title').'</a>';
		$breadcrumb = implode($raquo, $tblTmp);
		return $breadcrumb;
    }
}
?>