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
 * Class Products
 * @package XoopsModules\Myservices
 */
class Products extends Myservices\ServiceObject
{
    public function __construct()
    {
        $this->initVar('products_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('products_vat_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('products_categories_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('products_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_online', XOBJ_DTYPE_INT, null, false);
        $this->initVar('products_price', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_summary', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('products_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('products_quality_link', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image1', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image2', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image3', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image4', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image5', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image6', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image7', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image8', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image9', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_image10', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('products_duration', XOBJ_DTYPE_INT, null, false);
        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Renvoie le lien qui permet d'aller à la page d'un produit en tenant compte des options du module (avec ou sans URL rewriting)
     *
     * @return string L'URL à utiliser (mais sans les balises <a href="">, uniquement l'URL)
     */
    public function getProductLink()
    {
        $products_id    = $this->getVar('products_id');
        $products_title = $this->getVar('products_title', 'n');
        $url            = '';

        if (1 == \XoopsModules\Myservices\Utilities::getModuleOption('urlrewriting')) {    // On utilise l'url rewriting
            $url = MYSERVICES_URL . 'product-' . (int)$products_id . \XoopsModules\Myservices\Utilities::makeSeoUrl($products_title) . '.html';
        } else {    // Pas d'utilisation de l'url rewriting
            $url = MYSERVICES_URL . 'product.php?products_id=' . (int)$products_id;
        }

        return $url;
    }

    /**
     * Renvoie le TTC du produit courant (sans formatage)
     */
    public function getTTC()
    {
        $vat = null;
        global $vatArray, $hMsVat;
        if (is_array($vatArray)) {
            if (isset($vatArray[$this->getVar('products_vat_id')])) {
                $vat = $vatArray[$this->getVar('products_vat_id')];
            }
        } else {
            $tblVATs = [];
            $tblVATs = $hMsVat->getObjects(new \Criteria('vat_id', $this->getVar('products_vat_id'), '='));
            if (count($tblVATs) > 0) {
                $vat = $tblVATs[0];
            }
        }
        if (is_object($vat)) {
            return ((float)$this->getVar('products_price', 'e') * (float)$vat->getVar('vat_rate', 'e') / 100) + (float)$this->getVar('products_price', 'e');
        }

        return (float)$this->getVar('products_price');
    }

    /**
     * Renvoie le montant HT (sans formatage) du produit en tenant compte de la quantit� et du taux de TVA
     *
     * @param int    $quantity La quantité voulue de produit
     * @param  float $vatRate
     * @return float Le montant HT
     */
    public function getVATAmount($quantity, $vatRate)
    {
        $ht = (float)$this->getVar('products_price') * $quantity;

        return ($ht * $vatRate) / 100;
    }

    /**
     * Renvoie le taux de TVA associ� au produit courant
     *
     * @return float Le taux de TVA
     */
    public function getVATRate()
    {
        global $vatArray, $hMsVat;
        if (is_array($vatArray)) {
            if (isset($vatArray[$this->getVar('products_vat_id')])) {
                $vat = $vatArray[$this->getVar('products_vat_id')];
            }
        } else {
            $tblVATs = [];
            $tblVATs = $hMsVat->getObjects(new \Criteria('vat_id', $this->getVar('products_vat_id'), '='));
            if (count($tblVATs) > 0) {
                $vat = $tblVATs[0];
            }
        }
        if (is_object($vat)) {
            return $vat->getVar('vat_rate', 'e');
        }

        return 0;
    }

    /**
     * Formatage des données pour affichage
     * @param string $format
     * @return array
     */
    public function toArray($format = 's')
    {
        $ret = [];
        foreach ($this->vars as $k => $v) {
            $ret[$k] = $this->getVar($k, $format);
        }
        $ret['products_url']        = $this->getProductLink();
        $ret['products_ttc']        = $this->getTTC();
        $ret['products_href_title'] = \XoopsModules\Myservices\Utilities::makeHrefTitle($this->getVar('products_title'));
        $quantity                   = $this->getVar('products_duration');

        // Formattage des monnaies
        $currency = \XoopsModules\Myservices\Currency::getInstance();

        // La TVA formatée avec le bon nombre de décimales (sans signe monétaire)
        $ret['products_formatted_vatrate'] = $currency->amountInCurrency($this->getVATRate());

        // TTC et HT avec la monnaie au format long (Euro par exemple)
        $ret['products_displaylong_price']    = $currency->amountForDisplay($this->getVar('products_price', 'e') * $quantity);
        $ret['products_displaylong_pricettc'] = $currency->amountForDisplay($ret['products_ttc'] * $quantity);

        // TTC et HT avec la monnaie au format court (€ par exemple)
        $ret['products_displayshort_price']    = $currency->amountForDisplay($this->getVar('products_price', 'e') * $quantity, 's');
        $ret['products_displayshort_pricettc'] = $currency->amountForDisplay($ret['products_ttc'] * $quantity, 's');

        // TTC et HT formatés avec le bon nombre de décimales mais SANS signe monétaire
        $ret['products_calc_price']    = $currency->amountForDisplay($this->getVar('products_price', 'e') * $quantity, 's');
        $ret['products_calc_pricettc'] = $currency->amountForDisplay($ret['products_ttc'] * $quantity, 's');

        // URLs des images
        for ($i = 1; $i <= 10; ++$i) {
            $fieldName    = sprintf('products_image%d', $i);
            $imageUrlName = sprintf('products_image_url%d', $i);
            $value        = $this->getVar($fieldName);
            if ('' != xoops_trim($value)) {
                $ret[$imageUrlName] = XOOPS_UPLOAD_URL . '/' . $value;
            } else {
                $ret[$imageUrlName] = MYSERVICES_IMAGES_URL . 'blank.gif';
            }
        }

        return $ret;
    }
}
