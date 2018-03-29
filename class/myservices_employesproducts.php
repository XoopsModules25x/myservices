<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

//require XOOPS_ROOT_PATH.'/kernel/object.php';
if (!class_exists('myservices_ORM')) {
    require XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
}

class myservices_employesproducts extends myservices_Object
{
    public function __construct()
    {
        $this->initVar('employesproducts_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('employesproducts_employes_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('employesproducts_products_id', XOBJ_DTYPE_INT, null, false);
    }
}

class MyservicesMyservices_employesproductsHandler extends myservices_ORM
{
    public function __construct($db)
    {    //                                 Table                   Classe                              Id
        parent::__construct($db, 'myservices_employesproducts', 'myservices_employesproducts', 'employesproducts_id');
    }

    /**
     * Renvoie les identifiants des personnes qui fournissent un certain service (lié à un produit)
     *
     * @param integer $products_id Numéro du produit
     * @return array   Array of peoples IDs
     */
    public function getEmployeesIdForProduct($products_id)
    {
        $peopleList = [];
        $critere    = new \Criteria('employesproducts_products_id', $products_id, '=');
        $people     =& $this->getObjects($critere, true, true, 'employesproducts_employes_id');
        foreach ($people as $person) {
            $peopleList[] = $person->getVar('employesproducts_employes_id');
        }

        return $peopleList;
    }

    /**
     * Renvoie la liste des produits dont une personne assure les services
     *
     * @param integer $employesproducts_employes_id Identifiant de la personne dont on veut connaître les produits qu'elle traite
     * @return array   Identifiants des produits
     */
    public function getProductsFromEployee($employesproducts_employes_id)
    {
        $return   = $tmp = [];
        $criteria = new \Criteria('employesproducts_employes_id', $employesproducts_employes_id, '=');
        $tmp      =& $this->getObjects($criteria, false, true, 'employesproducts_products_id');
        if (count($tmp) > 0) {
            foreach ($tmp as $item) {
                $return[] = $item->getVar('employesproducts_products_id');
            }
        }

        return $return;
    }
}
