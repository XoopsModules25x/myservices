<?php

namespace XoopsModules\Myservices;

/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

//require_once XOOPS_ROOT_PATH.'/kernel/object.php';
//if (!class_exists('myservices_ORM')) {
//    require_once XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
//}

/**
 * Class CategoriesHandler
 * @package XoopsModules\Myservices
 */
class CategoriesHandler extends Myservices\ServiceORM
{
    /**
     * CategoriesHandler constructor.
     * @param $db
     */
    public function __construct($db)
    {    //                             Table                   Classe                  Id              Description
        parent::__construct($db, 'myservices_categories', Categories::class, 'categories_id', 'categories_title');
    }

    /**
     * Renvoie la liste des catégories filles d'une catégorie particulière
     *
     * @param int $categories_id Id de la catégorie dont on veut récupérer les filles
     * @return array Tableau d'objets catégories
     */
    public function getAllChild($categories_id)
    {
        require_once XOOPS_ROOT_PATH . '/class/tree.php';
        $allCategories = $tblChilds = [];
        $allCategories = $this->getItems();
        $mytree        = new \XoopsObjectTree($allCategories, 'categories_id', 'categories_pid');
        $tblChilds     = $mytree->getAllChild($categories_id);

        return $tblChilds;
    }

    /**
     * Renvoie la liste des catégories mères (les catégories de plus haut niveau qui n'ont pas de mère)
     *
     * @return array Objects of type categories
     */
    public function getMotherCategories()
    {
        $tblItems = [];
        $criteria = new \Criteria('categories_pid', 0, '=');
        $criteria->setSort('categories_title');
        $tblItems = $this->getObjects($criteria, true);

        return $tblItems;
    }

    /**
     * Renvoie un breadcrumb jusqu'à une catégorie déterminée
     *
     * @param Categories|object Objet  de type catégorie qui représente la catégorie courante
     * @param string $raquo Le "séparateur" à utiliser entre les chaines de caractères
     * @return string Le breadcrumb depuis la page d'index jusqu'à la catégorie courante
     */
    public function getBreadCrumb(Categories $currentCategory, $raquo = ' &raquo; ')
    {
        require_once XOOPS_ROOT_PATH . '/class/tree.php';
        $currentCategoryId = $currentCategory->getVar('categories_id');
        $allCategories     = [];
        $allCategories     = $this->getItems();
        $mytree            = new \XoopsObjectTree($allCategories, 'categories_id', 'categories_pid');

        $tblTmp       = $tblAncestors = [];
        $tblAncestors = array_reverse($mytree->getAllParent($currentCategoryId));
        $moduleName   = \XoopsModules\Myservices\Utilities::getModuleName();

        // Ajout de la page d'index avec le nom du module (permet de renvoyer vers la liste des catégories de niveau 1)
        $tblTmp[] = "<a href='" . MYSERVICES_URL . "index.php' title='" . \XoopsModules\Myservices\Utilities::makeHrefTitle($moduleName) . "'>" . $moduleName . '</a>';
        foreach ($tblAncestors as $item) {
            $tblTmp[] = "<a href='" . $item->getCategoryLink() . "' title='" . \XoopsModules\Myservices\Utilities::makeHrefTitle($item->getVar('categories_title')) . "'>" . $item->getVar('categories_title') . '</a>';
        }
        // Ajout de la catégorie courante
        $tblTmp[]   = "<a href='" . $currentCategory->getCategoryLink() . "' title='" . \XoopsModules\Myservices\Utilities::makeHrefTitle($currentCategory->getVar('categories_title')) . "'>" . $currentCategory->getVar('categories_title') . '</a>';
        $breadcrumb = implode($raquo, $tblTmp);

        return $breadcrumb;
    }
}
