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
//    require_once XOOPS_ROOT_PATH . '/modules/myservices/class/ServiceORM.php';
//}

/**
 * Class EmployeesProductsHandler
 * @package XoopsModules\Myservices
 */
class EmployeesProductsHandler extends Myservices\ServiceORM
{
    /**
     * EmployeesProductsHandler constructor.
     * @param $db
     */
    public function __construct($db)
    {    //                                 Table                   Classe                              Id
        parent::__construct($db, 'myservices_employeesproducts', EmployeesProducts::class, 'employeesproducts_id');
    }

    /**
     * Renvoie les identifiants des personnes qui fournissent un certain service (lié à un produit)
     *
     * @param int $products_id Numéro du produit
     * @return array   Array of peoples IDs
     */
    public function getEmployeesIdForProduct($products_id)
    {
        $peopleList = [];
        $critere    = new \Criteria('employeesproducts_products_id', $products_id, '=');
        $people     = $this->getObjects($critere, true, true, 'employeesproducts_employees_id');
        foreach ($people as $person) {
            $peopleList[] = $person->getVar('employeesproducts_employees_id');
        }

        return $peopleList;
    }

    /**
     * Renvoie la liste des produits dont une personne assure les services
     *
     * @param int $employeesproducts_employees_id Identifiant de la personne dont on veut connaître les produits qu'elle traite
     * @return array   Identifiants des produits
     */
    public function getProductsFromEployee($employeesproducts_employees_id)
    {
        $return   = $tmp = [];
        $criteria = new \Criteria('employeesproducts_employees_id', $employeesproducts_employees_id, '=');
        $tmp      = $this->getObjects($criteria, false, true, 'employeesproducts_products_id');
        if (count($tmp) > 0) {
            foreach ($tmp as $item) {
                $return[] = $item->getVar('employeesproducts_products_id');
            }
        }

        return $return;
    }
}
