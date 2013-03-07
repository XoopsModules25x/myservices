<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

/**
 * Classe responsable de la gestion de tout ce qui est relatif � Paypal
 */

if (!defined('XOOPS_ROOT_PATH')) {
	die("XOOPS root path not defined");
}

class myservices_paypal
{
	public $testMode;
	public $email;
	public $moneyCode;
	public $useIpn;
	public $passwordCancel;

	function __construct($testMode, $emailPaypal, $moneyCode, $ipn=false, $passwordCancel='')
	{
		$this->testMode = $testMode;
		$this->email = $emailPaypal;
		$this->moneyCode = $moneyCode;
		$this->useIpn = $ipn;
		$this->passwordCancel = $passwordCancel;
	}

	/**
	 * Renvoie l'url � utiliser en tenant compte du fait qu'on est en mode test ou pas
	 */
	 function getURL($securized=false)
	 {
	 	if(!$securized) {
	 		if($this->testMode == 1 ) {
   				return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			} else {
				return 'https://www.paypal.com/cgi-bin/webscr';
	 		}
	 	} else {
	 		if($this->testMode == 1 ) {
   				return 'www.sandbox.paypal.com';
			} else {
				return 'www.paypal.com';
	 		}
	 	}
	 }

	/**
	 * Formate le montant au format Paypal
	 */
	private function formatAmount($amount)
	{
		return sprintf("%.02f", $amount);
	}


	/**
	 * Renvoie les �l�ments � ajouter au formulaire en tant que zones cach�es
	 *
	 * @param integer $commmandId Num�ro de la commande
	 * @param float $ttc TTC � facturer
	 */
	function getFormContent($commandId, $ttc, $emailClient)
	{
		global $xoopsConfig;
		$ret = array();
		$ret['cmd'] = '_xclick';
		$ret['upload'] = '1';
		$ret['currency_code'] = $this->moneyCode;
		$ret['business'] = $this->email;
		$ret['return'] = MYSERVICES_URL.'thankyou.php';			// Page (g�n�rique) de remerciement apr�s paiement
		$ret['image_url'] = XOOPS_URL.'/images/logo.gif';
		$ret['cpp_header_image'] = XOOPS_URL.'/images/logo.gif';
		$ret['invoice'] = $commandId;
		$ret['item_name'] = _MYSERVICES_ORDER.$commandId.' - '.$xoopsConfig['sitename'];
		$ret['item_number'] =  $commandId;
		$ret['amount'] = $this->formatAmount($ttc);
		$ret['custom'] = $commandId;
		//$ret['rm'] = 2;	// Renvoyer les donn�es par POST (normalement)
		$ret['email'] = $emailClient;
		// paypal_pdt
		if(xoops_trim($this->passwordCancel) != '') {	// URL � laquelle le navigateur du client est ramen� si le paiement est annul�
			$ret['cancel_return'] = MYSERVICES_URL.'cancel-payment.php?id='.$this->passwordCancel;
		}
		if($this->useIpn == 1) {
			$ret['notify_url'] = MYSERVICES_URL.'paypal-notify.php';
		}
		return $ret;
	}
}
?>
