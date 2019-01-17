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

/**
 * Class CaddyHandler
 * @package XoopsModules\Myservices
 */
class CaddyHandler extends Myservices\ServiceORM
{
    /**
     * CaddyHandler constructor.
     * @param $db
     */
    public function __construct($db)
    {    //                             Table           Classe              Id
        parent::__construct($db, Caddy::class, 'Caddy', 'caddy_id');
    }

    /**
     * Renvoie les ID de commandes pour un produit acheté
     *
     * @param integer $product_id Identifiant du produit recherché
     * @return array Les ID des commandes dans lesquelles ce produit a été commandé
     */
    public function getCommandIdFromProduct($product_id)
    {
        $ret    = [];
        $sql    = 'SELECT caddy_orders_id FROM ' . $this->table . ' WHERE caddy_products_id = ' . (int)$product_id;
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[] = $myrow['caddy_orders_id'];
        }

        return $ret;
    }
}
