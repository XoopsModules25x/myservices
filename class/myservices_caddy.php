<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

//require XOOPS_ROOT_PATH.'/kernel/object.php';
if (!class_exists('myservices_ORM')) {
    require XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
}

class myservices_caddy extends myservices_Object
{
    public function __construct()
    {
        $this->initVar('caddy_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('caddy_products_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('caddy_employes_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('caddy_calendar_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('caddy_orders_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('caddy_price', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('caddy_vat_rate', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('caddy_start', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('caddy_end', XOBJ_DTYPE_TXTBOX, null, false);
    }
}

class MyservicesMyservices_caddyHandler extends myservices_ORM
{
    public function __construct($db)
    {    //                             Table           Classe              Id
        parent::__construct($db, 'myservices_caddy', 'myservices_caddy', 'caddy_id');
    }

    /**
	 * Renvoie les ID de commandes pour un produit acheté
     *
	 * @param integer $product_id Identifiant du produit recherché
	 * @return array Les ID des commandes dans lesquelles ce produit a été commandé
     */
    public function getCommandIdFromProduct($product_id)
    {
        $ret    = array();
        $sql    = 'SELECT caddy_orders_id FROM ' . $this->table . ' WHERE caddy_products_id = ' . (int)$product_id;
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow['caddy_orders_id'];
        }

        return $ret;
    }
}
