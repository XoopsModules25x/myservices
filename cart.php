<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

/**
 * Affichage et gestion du caddy
 */
require 'header.php';
$xoopsOption['template_main'] = 'myservices_cart.html';
require_once XOOPS_ROOT_PATH.'/header.php';
require_once MYSERVICES_PATH.'class/myservices_cart.php';

$myservicesCart = & myservices_Cart::getInstance();	// Pour gérer le panier
$vatArray = array();
$vatArray = $hMsVat->getItems();

$op = 'default';
if(isset($_POST['op'])) {
	$op = $_POST['op'];
} elseif(isset($_GET['op'])) {
	$op = $_GET['op'];
}

$products_id = 0;
if(isset($_POST['products_id'])) {
	$products_id = intval($_POST['products_id']);
} elseif(isset($_GET['products_id'])) {
	$products_id = intval($_GET['products_id']);
}

$employes_id = 0;
if(isset($_POST['employee'])) {
	$employes_id = intval($_POST['employee']);
} elseif(isset($_GET['employee'])) {
	$employes_id = intval($_GET['employee']);
}

$selectedDay = 0;
if(isset($_POST['selectedDay'])) {
	$selectedDay = intval($_POST['selectedDay']);
} elseif(isset($_GET['selectedDay'])) {
	$selectedDay = intval($_GET['selectedDay']);
}

$selectedMonth = 0;
if(isset($_POST['selectedMonth'])) {
	$selectedMonth = intval($_POST['selectedMonth']);
} elseif(isset($_GET['selectedMonth'])) {
	$selectedMonth = intval($_GET['selectedMonth']);
}

$selectedYear = 0;
if(isset($_POST['selectedYear'])) {
	$selectedYear = intval($_POST['selectedYear']);
} elseif(isset($_GET['selectedYear'])) {
	$selectedYear = intval($_GET['selectedYear']);
}

$duration = 0;
if(isset($_POST['duration'])) {
	$duration = $_POST['duration'];
} elseif(isset($_GET['duration'])) {
	$duration = $_GET['duration'];
}

$startingHour = '';
if(isset($_POST['selectedTime'])) {
	$startingHour = $_POST['selectedTime'];
} elseif(isset($_GET['selectedTime'])) {
	$startingHour = $_GET['selectedTime'];
}
if(!empty($startingHour)) {
	$startingHour = myservices_utils::normalyzeTime($startingHour);
}

$formatedDate = '';
if(!empty($selectedDay) && !empty($selectedMonth) && ! empty($selectedYear)) {
	$formatedDate = sprintf("%04d-%02d-%02d", $selectedYear, $selectedMonth, $selectedDay);
}

$xoopsTpl->assign('op', $op);
$xoopsTpl->assign('confEmpty', myservices_utils::javascriptLinkConfirm(_MYSERVICES_EMPTY_CART_SURE,true));
$xoopsTpl->assign('confirm_delete_item', myservices_utils::javascriptLinkConfirm(_MYSERVICES_EMPTY_ITEM_SURE, false));
$xoopsTpl->assign('module_name', myservices_utils::getModuleName());


// ********************************************************************************************************************
// Liste le contenu du caddy
// ********************************************************************************************************************
function listCart()
{
	global $xoopsTpl, $myservicesCart;

	$cartForTemplate = array();
	$emptyCart = false;
	$commandAmount = $vatAmount = $commandAmountTTC = 0;

	$myservicesCart->computeCart($cartForTemplate, $emptyCart, $commandAmount, $vatAmount, $commandAmountTTC);
	$currency = & myservices_currency::getInstance();

	$xoopsTpl->assign('emptyCart', $emptyCart);												// Caddy Vide ?
	$xoopsTpl->assign('caddieProducts', $cartForTemplate);									// Produits dans le caddy
	$xoopsTpl->assign('commandAmount', $currency->amountForDisplay($commandAmount));		// Montant HT de la commande
	$xoopsTpl->assign('vatAmount', $currency->amountForDisplay($vatAmount));				// Montant de la TVA
	$xoopsTpl->assign('goOn', MYSERVICES_URL);												// Adresse à utiliser pour continuer ses achats
	$xoopsTpl->assign('commandAmountTTC', $currency->amountForDisplay($commandAmountTTC));	// Montant TTC de la commande
}

// ********************************************************************************************************************
// ********************************************************************************************************************
// ********************************************************************************************************************
switch ($op)
{

	// ****************************************************************************************************************
	case 'add':	// Ajout d'un élément
	// ****************************************************************************************************************
		if($products_id == 0) {
			myservices_utils::redirect(_MYSERVICES_ERROR9, 'index.php', 4);
		}
		$product = null;
		$product = $hMsProducts->get($products_id);
		if(!is_object($product)) {
			myservices_utils::redirect(_MYSERVICES_ERROR9, 'index.php', 4);
		}

		if($employes_id == 0) {
			myservices_utils::redirect(_MYSERVICES_ERROR11, 'index.php', 4);
		}
		$employee = null;
		$employee = $hMsEmployes->get($employes_id);
		if(!is_object($product)) {
			myservices_utils::redirect(_MYSERVICES_ERROR11, 'index.php', 4);
		}

		if($duration == 0) {
			myservices_utils::redirect(_MYSERVICES_ERROR19, 'index.php', 4);
		}

		if($startingHour == '') {
			myservices_utils::redirect(_MYSERVICES_ERROR20, 'index.php', 4);
		}

		if($formatedDate == '') {
			myservices_utils::redirect(_MYSERVICES_ERROR21, 'index.php', 4);
		}

		$myservicesCart->addProduct($products_id, $duration, $employes_id, $startingHour, $formatedDate);
		$url = MYSERVICES_URL.'cart.php';
		header("Location: $url");	// Pour éviter de reposter le même produit lorsqu'on rafraichit la page
		listCart();
		break;


	// ****************************************************************************************************************
	case 'update':	// Recalcul des quantités
	// ****************************************************************************************************************
		$myservicesCart->updateQuantites();
		listCart();
		break;

	// ****************************************************************************************************************
	case 'delete':	// Suppression d'un élément
	// ****************************************************************************************************************
		$myservicesCart->deleteProduct($products_id);
		listCart();
		break;


	// ****************************************************************************************************************
	case 'empty':	// Suppression du contenu du caddy
	// ****************************************************************************************************************
		$myservicesCart->emptyCart();
		listCart();
		break;

	// ****************************************************************************************************************
	case 'default':	// Action par défaut
	// ****************************************************************************************************************
		listCart();
		break;
}

if (file_exists( MYSERVICES_PATH.'language/'.$xoopsConfig['language'].'/modinfo.php')) {
	require_once  MYSERVICES_PATH.'language/'.$xoopsConfig['language'].'/modinfo.php';
} else {
	require_once  MYSERVICES_PATH.'language/english/modinfo.php';
}

$title = _MI_MYSERVICES_SMNAME2.' - '.myservices_utils::getModuleName();
myservices_utils::setMetas($title, $title);
require_once XOOPS_ROOT_PATH.'/footer.php';
?>