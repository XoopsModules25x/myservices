<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

/**
 * Affichage et gestion du caddy
 */
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'myservices_cart.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
//require_once MYSERVICES_PATH . 'class/myservices_cart.php';

$myservicesCart = Myservices\Cart::getInstance();    // Pour gérer le panier
$vatArray       = [];
$vatArray       = $hMsVat->getItems();

$op    = \Xmf\Request::getCmd('op', 'default');

if (\Xmf\Request::hasVar('products_id', 'POST')) {
 $products_id = \Xmf\Request::getInt('products_id', 0, 'POST');
} else {
 $products_id = \Xmf\Request::getInt('products_id', 0, 'GET');
}


if (\Xmf\Request::hasVar('employee', 'POST')) {
 $employees_id = \Xmf\Request::getInt('employee', 0, 'POST');
} else {
 $employees_id = \Xmf\Request::getInt('employee', 0, 'GET');
}

$selectedDay = 0;
if (\Xmf\Request::hasVar('selectedDay', 'POST')) {
 $selectedDay = \Xmf\Request::getInt('selectedDay', 0, 'POST');
} elseif (\Xmf\Request::hasVar('selectedDay', 'GET')) {
 $selectedDay = \Xmf\Request::getInt('selectedDay', 0, 'GET');
}


if (\Xmf\Request::hasVar('selectedMonth', 'POST')) {
 $selectedMonth = \Xmf\Request::getInt('selectedMonth', 0, 'POST');
} else {
 $selectedMonth = \Xmf\Request::getInt('selectedMonth', 0, 'GET');
}


if (\Xmf\Request::hasVar('selectedYear', 'POST')) {
 $selectedYear = \Xmf\Request::getInt('selectedYear', 0, 'POST');
} else {
 $selectedYear = \Xmf\Request::getInt('selectedYear', 0, 'GET');
}

$duration = 0;
if (\Xmf\Request::hasVar('duration', 'POST')) {
    $duration = $_POST['duration'];
} elseif (\Xmf\Request::hasVar('duration', 'GET')) {
    $duration = $_GET['duration'];
}

$startingHour = '';
if (\Xmf\Request::hasVar('selectedTime', 'POST')) {
    $startingHour = $_POST['selectedTime'];
} elseif (\Xmf\Request::hasVar('selectedTime', 'GET')) {
    $startingHour = $_GET['selectedTime'];
}
if (!empty($startingHour)) {
    $startingHour =\XoopsModules\Myservices\Utilities::normalyzeTime($startingHour);
}

$formatedDate = '';
if (!empty($selectedDay) && !empty($selectedMonth) && !empty($selectedYear)) {
    $formatedDate = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $selectedDay);
}

$xoopsTpl->assign('op', $op);
$xoopsTpl->assign('confEmpty',\XoopsModules\Myservices\Utilities::javascriptLinkConfirm(_MYSERVICES_EMPTY_CART_SURE, true));
$xoopsTpl->assign('confirm_delete_item',\XoopsModules\Myservices\Utilities::javascriptLinkConfirm(_MYSERVICES_EMPTY_ITEM_SURE, false));
$xoopsTpl->assign('module_name',\XoopsModules\Myservices\Utilities::getModuleName());

// ********************************************************************************************************************
// Liste le contenu du caddy
// ********************************************************************************************************************
function listCart()
{
    global $xoopsTpl, $myservicesCart;

    $cartForTemplate = [];
    $emptyCart       = false;
    $commandAmount   = $vatAmount = $commandAmountTTC = 0;

    $myservicesCart->computeCart($cartForTemplate, $emptyCart, $commandAmount, $vatAmount, $commandAmountTTC);
    $currency = \XoopsModules\Myservices\Currency::getInstance();

    $xoopsTpl->assign('emptyCart', $emptyCart);                                                // Caddy Vide ?
    $xoopsTpl->assign('caddieProducts', $cartForTemplate);                                    // Produits dans le caddy
    $xoopsTpl->assign('commandAmount', $currency->amountForDisplay($commandAmount));        // Montant HT de la commande
    $xoopsTpl->assign('vatAmount', $currency->amountForDisplay($vatAmount));                // VAT amount
    $xoopsTpl->assign('goOn', MYSERVICES_URL);                                                // Adresse à utiliser pour continuer ses achats
    $xoopsTpl->assign('commandAmountTTC', $currency->amountForDisplay($commandAmountTTC));    // Tax amount of the order
}

// ********************************************************************************************************************
// ********************************************************************************************************************
// ********************************************************************************************************************
switch ($op) {

    // ****************************************************************************************************************
    case 'add':    // Ajout d'un élément
        // ****************************************************************************************************************
        if (0 == $products_id) {
           \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR9, 'index.php', 4);
        }
        $product = null;
        $product = $hMsProducts->get($products_id);
        if (!is_object($product)) {
           \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR9, 'index.php', 4);
        }

        if (0 == $employees_id) {
           \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR11, 'index.php', 4);
        }
        $employee = null;
        $employee = $hMsEmployees->get($employees_id);
        if (!is_object($product)) {
           \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR11, 'index.php', 4);
        }

        if (0 == $duration) {
           \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR19, 'index.php', 4);
        }

        if ('' == $startingHour) {
           \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR20, 'index.php', 4);
        }

        if ('' == $formatedDate) {
           \XoopsModules\Myservices\Utilities::redirect(_MYSERVICES_ERROR21, 'index.php', 4);
        }

        $myservicesCart->addProduct($products_id, $duration, $employees_id, $startingHour, $formatedDate);
        $url = MYSERVICES_URL . 'cart.php';
        header("Location: $url");    // Pour éviter de reposter le même produit lorsqu'on rafraichit la page
        listCart();
        break;

    // ****************************************************************************************************************
    case 'update':    // Recalcul des quantités
        // ****************************************************************************************************************
        $myservicesCart->updateQuantites();
        listCart();
        break;

    // ****************************************************************************************************************
    case 'delete':    // Suppression d'un élément
        // ****************************************************************************************************************
        $myservicesCart->deleteProduct($products_id);
        listCart();
        break;

    // ****************************************************************************************************************
    case 'empty':    // Suppression du contenu du caddy
        // ****************************************************************************************************************
        $myservicesCart->emptyCart();
        listCart();
        break;

    // ****************************************************************************************************************
    case 'default':    // Action par défaut
        // ****************************************************************************************************************
        listCart();
        break;
}

if (file_exists(MYSERVICES_PATH . 'language/' . $xoopsConfig['language'] . '/modinfo.php')) {
    require_once MYSERVICES_PATH . 'language/' . $xoopsConfig['language'] . '/modinfo.php';
} else {
    require_once MYSERVICES_PATH . 'language/english/modinfo.php';
}

$title = _MI_MYSERVICES_SMNAME2 . ' - ' .\XoopsModules\Myservices\Utilities::getModuleName();
\XoopsModules\Myservices\Utilities::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
