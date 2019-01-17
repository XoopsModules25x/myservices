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
 * Page appelée par Paypal après le paiement en ligne
 */
require_once __DIR__ . '/header.php';
$success     = true;
$datasPaypal = false;

$GLOBALS['xoopsOption']['template_main'] = 'myservices_thankyou.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
// require_once MYSERVICES_PATH . 'class/myservices_cart.php';

$cart = new Myservices\Cart();    // Pour gérer le panier
$cart->emptyCart();
$xoopsTpl->assign('success', $success);

$title = _MYSERVICES_PURCHASE_FINSISHED . ' - ' .\XoopsModules\Myservices\Utilities::getModuleName();
\XoopsModules\Myservices\Utilities::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
