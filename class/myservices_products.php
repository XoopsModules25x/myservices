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

class myservices_products extends myservices_Object
{
	function __construct()
	{
		$this->initVar('products_id',XOBJ_DTYPE_INT,null,false);
		$this->initVar('products_vat_id',XOBJ_DTYPE_INT,null,false);
		$this->initVar('products_categories_id',XOBJ_DTYPE_INT,null,false);
		$this->initVar('products_title',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_online',XOBJ_DTYPE_INT,null,false);
		$this->initVar('products_price',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_summary',XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('products_description',XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('products_quality_link',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image1',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image2',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image3',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image4',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image5',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image6',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image7',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image8',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image9',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_image10',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('products_duration',XOBJ_DTYPE_INT,null,false);
		// Pour autoriser le html
		$this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
	}

	/**
	 * Renvoie le lien qui permet d'aller à la page d'un produit en tenant compte des options du module (avec ou sans URL rewriting)
	 *
	 * @return string L'URL à utiliser (mais sans les balises <a href="">, uniquement l'URL)
	 */
	function getProductLink()
	{
		$products_id = $this->getVar('products_id');
		$products_title = $this->getVar('products_title', 'n');
		$url = '';

		if( myservices_utils::getModuleOption('urlrewriting') == 1 ) {	// On utilise l'url rewriting
			$url = MYSERVICES_URL.'product-'.intval($products_id).myservices_utils::makeSeoUrl($products_title).'.html';
		} else {	// Pas d'utilisation de l'url rewriting
			$url = MYSERVICES_URL.'product.php?products_id='.intval($products_id);
		}
		return $url;
	}

	/**
	 * Renvoie le TTC du produit courant (sans formatage)
	 */
	function getTTC()
	{
		$vat = null;
		global $vatArray, $hMsVat;
		if(is_array($vatArray)) {
			if(isset($vatArray[$this->getVar('products_vat_id')])) {
				$vat = $vatArray[$this->getVar('products_vat_id')];
			}
		} else {
			$tblVATs = array();
			$tblVATs = $hMsVat->getObjects(new Criteria('vat_id', $this->getVar('products_vat_id'), '='));
			if(count($tblVATs) > 0) {
				$vat = $tblVATs[0];
			}
		}
		if(is_object($vat)) {
			return (floatval($this->getVar('products_price', 'e')) * floatval($vat->getVar('vat_rate', 'e')) / 100) + floatval($this->getVar('products_price', 'e'));
		} else {
			return floatval($this->getVar('products_price'));
		}
	}

	/**
	 * Renvoie le montant HT (sans formatage) du produit en tenant compte de la quantité et du taux de TVA
	 *
	 * @param integer $quantity La quantité voulue de produit
	 * @param floatval $vatRat Le taux de TVA
	 * @return float Le montant HT
	 */
	function getVATAmount($quantity, $vatRate)
	{
		$ht = floatval($this->getVar('products_price')) * $quantity;
		return ($ht * $vatRate) / 100;
	}

	/**
	 * Renvoie le taux de TVA associé au produit courant
	 *
	 * @return floatval Le taux de TVA
	 */
	function getVATRate()
	{
		global $vatArray, $hMsVat;
		if(is_array($vatArray)) {
			if(isset($vatArray[$this->getVar('products_vat_id')])) {
				$vat = $vatArray[$this->getVar('products_vat_id')];
			}
		} else {
			$tblVATs = array();
			$tblVATs = $hMsVat->getObjects(new Criteria('vat_id', $this->getVar('products_vat_id'), '='));
			if(count($tblVATs) > 0) {
				$vat = $tblVATs[0];
			}
		}
		if(is_object($vat)) {
			return $vat->getVar('vat_rate', 'e');
		} else {
			return 0;
		}
	}

	/**
	 * Formatage des données pour affichage
	 */
    function toArray($format = 's')
    {
		$ret = array();
		foreach ($this->vars as $k => $v) {
			$ret[$k] = $this->getVar($k, $format);
		}
		$ret['products_url'] = $this->getProductLink();
		$ret['products_ttc'] = $this->getTTC();
		$ret['products_href_title'] = myservices_utils::makeHrefTitle($this->getVar('products_title'));
		$quantity = $this->getVar('products_duration');

		// Formattage des monnaies
		$currency = & myservices_currency::getInstance();

		// La TVA formatée avec le bon nombre de décimales (sans signe monétaire)
		$ret['products_formatted_vatrate'] = $currency->amountInCurrency($this->getVATRate());

		// TTC et HT avec la monnaie au format long (Euro par exemple)
		$ret['products_displaylong_price'] = $currency->amountForDisplay($this->getVar('products_price', 'e') * $quantity);
		$ret['products_displaylong_pricettc'] =$currency->amountForDisplay($ret['products_ttc'] * $quantity);

		// TTC et HT avec la monnaie au format court (€ par exemple)
		$ret['products_displayshort_price'] = $currency->amountForDisplay($this->getVar('products_price', 'e') * $quantity, 's');
		$ret['products_displayshort_pricettc'] = $currency->amountForDisplay($ret['products_ttc'] * $quantity, 's');

		// TTC et HT formatés avec le bon nombre de décimales mais SANS signe monétaire
		$ret['products_calc_price'] = $currency->amountForDisplay($this->getVar('products_price', 'e') * $quantity, 's');
		$ret['products_calc_pricettc'] = $currency->amountForDisplay($ret['products_ttc'] * $quantity, 's');

		// URLs des images
		for($i=1; $i<=10; $i++) {
			$fieldName = sprintf("products_image%d",$i);
			$imageUrlName = sprintf("products_image_url%d",$i);
			$value = $this->getVar($fieldName);
			if(xoops_trim($value) != '') {
				$ret[$imageUrlName] = XOOPS_UPLOAD_URL.'/'.$value;
			} else {
				$ret[$imageUrlName] = MYSERVICES_IMAGES_URL.'blank.gif';
			}
		}

		return $ret;
    }
}


class MyservicesMyservices_productsHandler extends myservices_ORM
{
	function __construct($db)
	{	//								Table					Classe			 	Id			Description
		parent::__construct($db, 'myservices_products', 'myservices_products', 'products_id', 'products_title');
	}

	/**
	 * Renvoie la liste des produits mais par catégorie
	 *
	 * @return array La liste des produits avec regroupement par catégorie
	 */
	function getProductsPerCategory()
	{
		require_once 'lite.php';
		$limit = $start = 0;
		$ret = array();
		$sql = 'SELECT * FROM '.$this->table.' WHERE products_online = 1 ORDER BY products_categories_id, products_title';

        $Cache_Lite = new Cache_Lite($this->cacheOptions);
        $id = $this->_getIdForCache($sql, $start, $limit);
        $cacheData = $Cache_Lite->get($id);
        if ($cacheData === false) {
	        $result = $this->db->query($sql);
        	if (!$result) {
            	return $ret;
        	}
        	while ($myrow = $this->db->fetchArray($result)) {
	            $obj =& $this->create(false);
            	$obj->assignVars($myrow);
				$ret[$myrow['products_categories_id']][] = & $obj;
				unset($obj);
        	}
        	$Cache_Lite->save($ret);
        	return $ret;
        } else {
        	return $cacheData;
        }
	}

	/**
	 * Renvoie le nombre total de produits appartenants à une catégorie donnée
	 *
	 * @param integer $categoryid Indentifiant de la catégorie
	 * @return integer Le nombre de produits de cette catégorie
	 */
	function getProductsCountFromCategory($categoryId)
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('products_categories_id', $categoryId, '='));
		$criteria->add(new Criteria('products_online', 1, '='));
		return $this->getCount($criteria);
	}

	/**
	 * Renvoie la liste des produits appartenants à une catégorie spécifique
	 *
	 * @param integer $categoryId Identifiant de la catégorie dont on veut récupérer les produits
	 * @param integer $start Position de départ
	 * @param integer $limit Nombre maximum de produits à renvoyer
	 * @param string $sort Champ à utiliser pour trier les produits
	 * @return array La liste des produits de la catégorie
	 */
	function getProductsFromCategory($categoryId, $start=0, $limit=0, $sort='products_title')
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('products_categories_id', $categoryId, '='));
		$criteria->add(new Criteria('products_online', 1, '='));
		$criteria->setStart($start);
		$criteria->setLimit($limit);
		$criteria->setSort($sort);
		return $this->getObjects($criteria);
	}

	/**
	 * Renvoie la liste des produits actifs parmi une liste de produits (via leur ID)
	 *
	 * @param array $products_id	Liste des produits sous la forme d'un tableau
	 * @return array	La liste des produits actifs (sous la forme d'objets)
	 */
	function getOnlineProductsFromId($products_id)
	{
		$ret = array();
		if(is_array($products_id)) {
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('products_online', 1, '='));
			$criteria->add(new Criteria('products_id', '('.implode(',', $products_id).')', 'IN'));
			$ret = $this->getObjects($criteria);
		}
		return $ret;
	}
}
?>