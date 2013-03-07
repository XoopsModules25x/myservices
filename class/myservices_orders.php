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

define("MYSERVICES_ORDER_NOINFORMATION", 0);	// Pas encore d'informations sur la commande
define("MYSERVICES_ORDER_VALIDATED", 1);		// Commande validée par Paypal
define("MYSERVICES_ORDER_PENDING", 2);			// En attente
define("MYSERVICES_ORDER_FAILED", 3);			// Echec
define("MYSERVICES_ORDER_CANCELED", 4);			// Annulée
define("MYSERVICES_ORDER_FRAUD", 5);			// Fraude

class myservices_orders extends myservices_Object
{
	function __construct()
	{
		$this->initVar('orders_id',XOBJ_DTYPE_INT,null,false);
		$this->initVar('orders_uid',XOBJ_DTYPE_INT,null,false);
		$this->initVar('orders_date',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_state',XOBJ_DTYPE_INT,null,false);
		$this->initVar('orders_ip',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_firstname',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_lastname',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_address',XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('orders_zip',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_town',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_country',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_telephone',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_email',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_articles_count',XOBJ_DTYPE_INT,null,false);
		$this->initVar('orders_total',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_password',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('orders_cancel',XOBJ_DTYPE_TXTBOX,null,false);
	}
}


class MyservicesMyservices_ordersHandler extends myservices_ORM
{
	function __construct($db)
	{	//							Table					Classe			 	Id
		parent::__construct($db, 'myservices_orders', 'myservices_orders', 'orders_id');
	}

	/**
	 * Validation d'une commande (changement d'état)
	 *
	 * @param integer $cmd_id Identifiant de la commande
	 * @param array $qualityLinks Lien vers les formulaires de qualité
	 * @return array Tableau contenant les textes à envoyer par email à la personne (les textes des produits)
	 */
	function validateOrder($cmd_id, &$qualityLinks)
	{
		global $hMsCaddy, $hMsCalendar, $hMsEmployes, $hMsProducts, $hMsVat, $hMsPrefs;
		$cmd_id = intval($cmd_id);
		$retval = $elementsCommande = array();
		$commande = null;
		$commande = $this->get($cmd_id);
		if(is_object($commande)) {
			$commande->setVar('orders_state', MYSERVICES_ORDER_VALIDATED);
			$this->insert($commande, true);
		}
		$elementsCommande[] = _MYSERVICES_CLIENT_INFO;
		$elementsCommande[] = $commande->getVar('orders_firstname').' '.$commande->getVar('orders_lastname');
		$elementsCommande[] = $commande->getVar('orders_address');
		$elementsCommande[] = $commande->getVar('orders_zip').' '.$commande->getVar('orders_town');
		$elementsCommande[] = $commande->getVar('orders_telephone');

		// Récupération des services réservés
		$tblServices = array();
		$tblServices = $hMsCaddy->getObjects(new Criteria('caddy_orders_id', $cmd_id, '='));
		foreach($tblServices as $service) {	// Boucle sur les éléments du panier
			$employee = $product = null;
			$employee = $hMsEmployes->get($service->getVar('caddy_employes_id'));
			$product = $hMsProducts->get($service->getVar('caddy_products_id'));

			$calendar = $hMsCalendar->create(true);
			$calendar->setVar('calendar_status', CALENDAR_STATUS_WORK);
			$calendar->setVar('calendar_employes_id', $service->getVar('caddy_employes_id'));
			$calendar->setVar('calendar_start', $service->getVar('caddy_start'));
			$calendar->setVar('calendar_end', $service->getVar('caddy_end'));
			$calendar->setVar('calendar_products_id', $service->getVar('caddy_products_id'));
			$res = $hMsCalendar->insert($calendar, true);
			if($res) {	// Mise à jour du caddy (lien caddy <-> calendar)
				$service->setVar('caddy_calendar_id', $calendar->getVar('calendar_id'));
				$hMsCaddy->insert($service, true);
			}

			// Liens vers les formulaires de qualité
			$qualityLinks[] = $product->getVar('products_quality_link');
			// Doit on prévenir les salariés ?
			if(myservices_utils::getModuleOption('email_employees') == 1 && xoops_trim($employee->getVar('employes_email')) != '') {
				$recipients = $msg = $elementsService = array();
				$recipients = myservices_utils::getEmailsFromGroup(myservices_utils::getModuleOption('grp_sold'));	// Copie aux responsables du site
				$recipients[] = $employee->getVar('employes_email'); // Plus le (la) salarié(e)

				$elementsService[] = _MYSERVICES_SERVICE.' : '.$product->getVar('products_title');
				$elementsService[] = _MYSERVICES_STARTING_DATE.' : '.myservices_utils::sqlDateTimeToFrench($service->getVar('caddy_start'));
				$elementsService[] = _MYSERVICES_ENDING_DATE.' : '.myservices_utils::sqlDateTimeToFrench($service->getVar('caddy_end'));

				$msg['DETAIL'] = implode("\n",$elementsCommande)."\n\n".implode("\n", $elementsService);
				$msg['DETAIL'] = myservices_utils::textForEmail($msg['DETAIL']);
				myservices_utils::sendEmailFromTpl('employee_service.tpl', $recipients, _MYSERVICES_ALERT, $msg);
			}
			$retval[] = _MYSERVICES_SERVICE.' : '.$product->getVar('products_title');
			$retval[] = _MYSERVICES_STARTING_DATE.' : '.myservices_utils::sqlDateTimeToFrench($service->getVar('caddy_start'));
			$retval[] = _MYSERVICES_ENDING_DATE.' : '.myservices_utils::sqlDateTimeToFrench($service->getVar('caddy_end'));
			$retval[] = _MYSERVICES_PRODUCT_PRICETTC.' : '.$service->getVar('caddy_price');
			$retval[] = "\n";
		}
		return $retval;
	}
}
?>