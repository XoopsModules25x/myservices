<?php namespace XoopsModules\Myservices;

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
 * Class ProductsHandler
 * @package XoopsModules\Myservices
 */
class ProductsHandler extends Myservices\ServiceORM
{
    /**
     * ProductsHandler constructor.
     * @param $db
     */
    public function __construct($db)
    {    //                             Table                   Classe              Id          Description
        parent::__construct($db, 'myservices_products', Products::class, 'products_id', 'products_title');
    }

    /**
     * Renvoie la liste des produits mais par catégorie
     *
     * @return array La liste des produits avec regroupement par catégorie
     */
    public function getProductsPerCategory()
    {
        // require_once __DIR__ . '/lite.php';
        $limit = $start = 0;
        $ret   = [];
        $sql   = 'SELECT * FROM ' . $this->table . ' WHERE products_online = 1 ORDER BY products_categories_id, products_title';

        $CacheLite = new CacheLite($this->cacheOptions);
        $id         = $this->_getIdForCache($sql, $start, $limit);
        $cacheData  = $CacheLite->get($id);
        if (false === $cacheData) {
            $result = $this->db->query($sql);
            if (!$result) {
                return $ret;
            }
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $obj = $this->create(false);
                $obj->assignVars($myrow);
                $ret[$myrow['products_categories_id']][] =& $obj;
                unset($obj);
            }
            $CacheLite->save($ret);

            return $ret;
        } else {
            return $cacheData;
        }
    }

    /**
     * Renvoie le nombre total de produits appartenants à une catégorie donnée
     *
     * @param int $categoryId Indentifiant de la catégorie
     * @return int Le nombre de produits de cette catégorie
     */
    public function getProductsCountFromCategory($categoryId)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('products_categories_id', $categoryId, '='));
        $criteria->add(new \Criteria('products_online', 1, '='));

        return $this->getCount($criteria);
    }

    /**
     * Renvoie la liste des produits appartenants à une catégorie spécifique
     *
     * @param integer $categoryId Identifiant de la catégorie dont on veut récupérer les produits
     * @param integer $start      Position de départ
     * @param integer $limit      Nombre maximum de produits à renvoyer
     * @param string  $sort       Champ à utiliser pour trier les produits
     * @return array La liste des produits de la catégorie
     */
    public function getProductsFromCategory($categoryId, $start = 0, $limit = 0, $sort = 'products_title')
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('products_categories_id', $categoryId, '='));
        $criteria->add(new \Criteria('products_online', 1, '='));
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $criteria->setSort($sort);

        return $this->getObjects($criteria);
    }

    /**
     * Renvoie la liste des produits actifs parmi une liste de produits (via leur ID)
     *
     * @param  array $products_id Liste des produits sous la forme d'un tableau
     * @return array La liste des produits actifs (sous la forme d'objets)
     */
    public function getOnlineProductsFromId($products_id)
    {
        $ret = [];
        if (is_array($products_id)) {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('products_online', 1, '='));
            $criteria->add(new \Criteria('products_id', '(' . implode(',', $products_id) . ')', 'IN'));
            $ret = $this->getObjects($criteria);
        }

        return $ret;
    }
}
