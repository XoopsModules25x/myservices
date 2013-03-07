<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id
 * ****************************************************************************
 */

if (!defined('XOOPS_ROOT_PATH')) {
	die("XOOPS root path not defined");
}

require XOOPS_ROOT_PATH.'/class/xoopsobject.php';
if (!class_exists('myservices_ORM')) {
	require XOOPS_ROOT_PATH.'/modules/myservices/class/PersistableObjectHandler.php';
}

class myservices_employes extends myservices_Object
{
	function __construct()
	{
		$this->initVar('employes_id',XOBJ_DTYPE_INT,null,false);
		$this->initVar('employes_firstname',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('employes_lastname',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('employes_email',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('employes_bio',XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('employes_photo1',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('employes_photo2',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('employes_photo3',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('employes_photo4',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('employes_photo5',XOBJ_DTYPE_TXTBOX,null,false);
		$this->initVar('employes_isactive',XOBJ_DTYPE_INT,null,false);

		// Pour autoriser le html
		$this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
	}

	/**
	 * Returns the current employee's fullname
	 */
	function getEmployeeFullName()
	{
		return $this->getVar('employes_lastname').' '.$this->getVar('employes_firstname');
	}

	/**
	 * Returns the link to use to go to an employee according to the module's options (with or without URL rewriting)
	 * @return string the html link to go to the category
	 */
	function getEmployeeLink()
	{
		$employee_id = $this->getVar('employes_id');
		$employee_fullname = $this->getEmployeeFullName();
		$url = '';

		if( myservices_utils::getModuleOption('urlrewriting') == 1 ) {	// On utilise l'url rewriting
			$url = MYSERVICES_URL.'employee-'.intval($employee_id).myservices_utils::makeSeoUrl($employee_fullname).'.html';
		} else {	// Pas d'utilisation de l'url rewriting
			$url = MYSERVICES_URL.'employee.php?employes_id='.intval($employee_id);
		}
		return $url;
	}

	/**
	 * Returns data formated
	 *
	 * @param string $format Format de retour des donn�es (en accord avec les param�tres de la fonction getVar() de XoopsObject)
	 * @return array Formated datas
	 */
    function toArray($format = 's')
    {
		$ret = array();
		foreach ($this->vars as $k => $v) {
			$ret[$k] = $this->getVar($k, $format);
		}
		$ret['employes_href_title'] = myservices_utils::makeHrefTitle($this->getEmployeeFullName());
		$ret['employes_fullname'] = $this->getEmployeeFullName();
		$ret['employes_link'] = $this->getEmployeeLink();
		for($i=1; $i<=4; $i++) {
			if(xoops_trim($this->getVar('employes_photo'.$i)) != '') {
				$ret['employes_photo'.$i.'url'] = XOOPS_UPLOAD_URL.'/'.$this->getVar('employes_photo'.$i);
			} else {
				$ret['employes_photo'.$i.'url'] = MYSERVICES_IMAGES_URL.'blank.gif';
			}
		}
		return $ret;
    }
}


class MyservicesMyservices_employesHandler extends myservices_ORM
{
	function __construct($db)
	{	//							Table					Classe			 		Id			Description
		parent::__construct($db, 'myservices_employes', 'myservices_employes', 'employes_id', 'employes_lastname');
	}

	/**
	 * Renvoie la liste des personnes qui fournissent un service li� � un produit
	 *
	 * @param integer $products_id Num�ro du produit
	 * @return array Objets de type employ�s (cl� = ID employ�, valeur = objet employ�)
	 */
	function getEmployeesForProduct($products_id)
	{
		global $hMsEmployesproducts;
		$peopleList = $employees = array();
		$peopleList = $hMsEmployesproducts->getEmployeesIdForProduct($products_id);
		if( count($peopleList) > 0 ) {	// On a la liste de toutes les personnes, reste � prendre les personnes actives
			$criteriaCompo = new CriteriaCompo();
			$criteriaCompo->add(new Criteria('employes_isactive', 1, '='));
			$criteriaCompo->add(new Criteria('employes_id', '('.implode(',', $peopleList).')', 'IN'));
			$employees = $this->getObjects($criteriaCompo, true);
		}
		return $employees;
	}

	/**
	 * Renvoie la liste des employ�s actifs
	 *
	 * @return array Objets de type employ�s
	 */
	function getActiveEmployees()
	{
		$critere = new Criteria('employes_isactive', 1, '=');
		if( isset($this->identifierName) && trim($this->identifierName) != '') {
			$order = $this->identifierName;
		} else {
			$order = $this->keyName;
		}
		$tblItems = array();
		$critere->setOrder($order);
		$tblItems = $this->getObjects($critere);
		return $tblItems;
	}

	/**
	 * Renvoie la liste des produits dont une personne assume le service
	 *
	 * @param unknown_type $employes_id
	 * @return array	Objets de type produits
	 */
	function getServicesFromEmployee($employes_id)
	{
		global $hMsEmployesproducts, $hMsProducts;
		$tblProductsIds = $tblProducts = array();
		$tblProductsIds = $hMsEmployesproducts->getProductsFromEployee($employes_id);
		if(count($tblProductsIds) > 0) {
			$tblProducts = $hMsProducts->getOnlineProductsFromId($tblProductsIds);
		}
		return $tblProducts;
	}

	/**
	 * Indique si une personne est en cong�s � une date donn�e
	 *
	 * @param integer $year Ann�e
	 * @param integer $month Mois
	 * @param integer $day Jour
	 * @param string $startingHour Heure de d�but
	 * @param integer $duration Dur�e en heures
	 * @param integer $employes_id Identifiant de l'employ�(e)
	 * @return boolean Vrai si la personne est en cong�s sinon faux
	 */
	function isEmployeeInHoliday($year, $month, $day, $startingHour, $duration, $employes_id)
	{
		global $hMsCalendar;
		$startingHour = myservices_utils::normalyzeTime($startingHour);
		$requestedDate = sprintf("%04d-%02d-%02d %s", $year, $month, $day, $startingHour);
		// 1) Recherche du "en plein dedans"
		$sql = "SELECT COUNT(*) as cpt FROM ".$this->db->prefix('myservices_calendar')." WHERE calendar_status = ".CALENDAR_STATUS_HOLIDAY." AND calendar_employes_id = ".intval($employes_id)." AND (calendar_start <= '".$requestedDate."' AND '".$requestedDate."' <= calendar_end)";
		$result = $this->db->query($sql);
		if($result) {
			list($count) = $this->db->fetchRow($result);
		} else {
			$count = 0;
		}
/*
		$criteriaCompo = new CriteriaCompo();
		$criteriaCompo->add(new Criteria('calendar_status', CALENDAR_STATUS_HOLIDAY, '='));
		$criteriaCompo->add(new Criteria('calendar_employes_id', $employes_id, '='));
		$criteriaCompo->add(new Criteria('calendar_start', $requestedDate, '<='));
		$criteriaCompo->add(new Criteria("'".$requestedDate."'", 'calendar_end', '<='));
		$count = 0;
		$count = $hMsCalendar->getCount($criteriaCompo);
		unset($criteriaCompo);
*/

		if($count > 0) {
			return true;
		}

		// 2) Recherche de la p�riode o� l'heure de d�but + la dur�e tombe pendant les vacances
		$hours = intval(substr($startingHour,0,2));
		$minutes = intval(substr($startingHour,3,2));
		$seconds = intval(substr($startingHour,6,2));
		$timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year) + ($duration * 3600);
		$calculedDate = date("Y-m-d H:i:s", $timestamp);

		$criteriaCompo = new CriteriaCompo();
		$criteriaCompo->add(new Criteria('calendar_status', CALENDAR_STATUS_HOLIDAY, '='));
		$criteriaCompo->add(new Criteria('calendar_employes_id', $employes_id, '='));
		$criteriaCompo->add(new Criteria('calendar_start', $calculedDate, '>='));
		$criteriaCompo->add(new Criteria('calendar_end', $calculedDate, '<='));
		$count = 0;
		$count = $hMsCalendar->getCount($criteriaCompo);
		if($count > 0) {
			return true;
		}
		return false;
	}

	/**
	 * Indique si une personne est d�j� au travail pour une date donn�e
	 *
	 * @param integer $year Ann�e
	 * @param integer $month Mois
	 * @param integer $day Jour
	 * @param string $startingHour Heure de d�but
	 * @param integer $duration Dur�e en heures
	 * @param integer $employes_id Identifiant de l'employ�(e)
	 * @return boolean Vrai si la personne est d�j� au travail (ou si elle n'est plus au travail mais elle n'a pas le temps de battement)
	 */
	 function isEmployeeWorking($year, $month, $day, $startingHour, $duration, $employes_id)
	{
		global $hMsCalendar;
		$battement = myservices_utils::getModuleOption('battement') * 60;	// Conversion en secondes
		$startingHour = myservices_utils::normalyzeTime($startingHour);
		$requestedDate = sprintf("%04d-%02d-%02d %s", $year, $month, $day, $startingHour);
		$shortRequestedDate = sprintf("%04d-%02d-%02d", $year, $month, $day);
		// 1) Recherche du "en plein dedans" (L'heure demand�e tombe dans un cr�neau horaire o� la personne travaille d�j�)
		$sql = "SELECT COUNT(*) as cpt FROM ".$this->db->prefix('myservices_calendar')." WHERE calendar_status = ".CALENDAR_STATUS_WORK." AND calendar_employes_id = ".intval($employes_id)." AND (calendar_start <= '".$requestedDate."' AND '".$requestedDate."' <= calendar_end)";
		$result = $this->db->query($sql);
		if($result) {
			list($count) = $this->db->fetchRow($result);
		} else {
			$count = 0;
		}

/*
		$criteriaCompo = new CriteriaCompo();
		$criteriaCompo->add(new Criteria('calendar_status', CALENDAR_STATUS_WORK, '='));
		$criteriaCompo->add(new Criteria('calendar_employes_id', $employes_id, '='));
		$criteriaCompo->add(new Criteria('calendar_start', $requestedDate, '>='));
		$criteriaCompo->add(new Criteria('calendar_end', $requestedDate, '<='));
		$count = 0;
		$count = $hMsCalendar->getCount($criteriaCompo);
		unset($criteriaCompo);
*/
		if($count > 0) {
			return true;
		}

		// 2) Recherche de la p�riode o� l'heure de d�but + la dur�e tombe pendant une autre p�riode
		$hours = intval(substr($startingHour,0,2));
		$minutes = intval(substr($startingHour,3,2));
		$seconds = intval(substr($startingHour,6,2));
		$timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);	// timestamp de la date et heure demand�e
		$calculedDate = date("Y-m-d H:i:s", $timestamp + ($duration * 3600));


		$criteriaCompo = new CriteriaCompo();
		$criteriaCompo->add(new Criteria('calendar_status', CALENDAR_STATUS_WORK, '='));
		$criteriaCompo->add(new Criteria('calendar_employes_id', $employes_id, '='));
		$criteriaCompo->add(new Criteria('calendar_start', $calculedDate, '>='));
		$criteriaCompo->add(new Criteria('calendar_end', $calculedDate, '<='));
		$count = 0;
		$count = $hMsCalendar->getCount($criteriaCompo);
		if($count > 0) {
			return true;
		}
		// 3) V�rifications par rapport au temps de battement et aux r�servations d�j� faites ce m�me jour
		unset($criteriaCompo);
		$criteriaCompo = new CriteriaCompo();
		$criteriaCompo->add(new Criteria('calendar_status', CALENDAR_STATUS_WORK, '='));
		$criteriaCompo->add(new Criteria('calendar_employes_id', $employes_id, '='));
		$criteriaCompo->add(new Criteria('date(calendar_start)', $shortRequestedDate, '='));
		$reservations = array();
		$reservations = $hMsCalendar->getObjects($criteriaCompo);
		foreach($reservations as $reservation) {
			$startingHour = strtotime($reservation->getVar('calendar_start')) - $battement;
			$endingHour =  strtotime($reservation->getVar('calendar_end')) + $battement;
			if($timestamp > $startingHour || $timestamp < $endingHour) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Indique si une personne est disponible pour un service, pour une date et pour une dur�e
	 * @param integer $year L'ann�e
	 * @param integer $month Le mois
	 * @param integer $day Le jour
	 * @param string $hour L'heure de d�but de r�servation (au format 17:10)
	 * @param integer $duration La dur�e de la r�servation
	 * @param integer $products_id L'identifiant du produit
	 * @param integer $employes_id L'identifiant du salari�
	 * @return boolean Vrai si la personne est disponible sinon faux
	 */
	function isEmployeeAvailable($year, $month, $day, $hour, $duration, $products_id, $employes_id)
	{
		global $hMsProducts, $hMsCalendar, $hMsPrefs;
		$product = $employee = null;
		// R�cup�ration du produit
		$product = $hMsProducts->get($products_id);
		if(!is_object($product)) {
			//file_put_contents('verif.txt',' produit introuvable');
			return false;
		}
		$employee = $this->get($employes_id);
		if(!is_object($employee)) {
			//file_put_contents('verif.txt',' employ� introuvable');
			return false;
		}

		// 1) Est ce que la personne est active ?
		if($employee->getVar('employes_isactive') != 1) {
			//file_put_contents('verif.txt',' employ� inactif');
			return false;
		}

		$hour = myservices_utils::normalyzeTime($hour);

		// 2) La personne est en vacances ?
		if($this->isEmployeeInHoliday($year, $month, $day, $hour, $duration, $employes_id)) {
			//file_put_contents('verif.txt',' employ� en vacances');
			return false;
		}
		// 3) Travaille d�j� ?
		if($this->isEmployeeWorking($year, $month, $day, $hour, $duration, $employes_id)) {
			//file_put_contents('verif.txt',' travaille d�j�');
			return false;
		}

		// 4) Heure de d�but + dur�e = en dehors des heures de travail ?
		$hours = intval(substr($hour,0,2));
		$minutes = intval(substr($hour,3,2));
		$seconds = intval(substr($hour,6,2));
		$timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year) + ($duration * 3600);
		$calculdatedHour = date("H:i:s", $timestamp);
		if(!$hMsPrefs->isStoreOpen($year, $month, $day, $calculdatedHour)) {
			//file_put_contents('verif.txt',' magasin pas ouvert');
			return false;
		}

		// 5) Toute la dur�e de la prestation tombe pendant les heures de travail ?
		$hours = intval(substr($hour,0,2));
		$minutes = intval(substr($hour,3,2));
		$seconds = intval(substr($hour,6,2));
		$timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
		for($i=1; $i<=$duration; $i++) {
			$newDate = getdate($timestamp);
			$formatedHour = sprintf("%02d:%02d:%02d", $newDate['hours'], $newDate['minutes'], $newDate['seconds']);
			if(!$hMsPrefs->isStoreOpen($newDate['year'], $newDate['mon'], $newDate['mday'], $formatedHour, true)) {
				//file_put_contents('verif.txt'," Une heure tombe pendant les heures de fermeture du magasin ".$formatedHour);
				return false;
			}
			$timestamp += 3600;
		}
		return true;
	}
}
?>