<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id: thankyou.php 11 2007-10-24 20:32:10Z hthouzard $
 * ****************************************************************************
 */

/**
 * Page appelée par Paypal après le paiement en ligne
 */
require 'header.php';
$success = true;
$datasPaypal = false;

$xoopsOption['template_main'] = 'myservices_thankyou.html';
require_once XOOPS_ROOT_PATH.'/header.php';
require_once MYSERVICES_PATH.'class/myservices_cart.php';

$cart = new myservices_Cart();	// Pour gérer le panier
$cart->emptyCart();
$xoopsTpl->assign('success', $success);

$title = _MYSERVICES_PURCHASE_FINSISHED.' - '.myservices_utils::getModuleName();
myservices_utils::setMetas($title, $title);
require_once(XOOPS_ROOT_PATH.'/footer.php');
?>
