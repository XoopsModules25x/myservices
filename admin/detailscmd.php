<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * ****************************************************************************
 */

use XoopsModules\Myservices;

require_once  dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require_once  dirname(__DIR__) . '/include/common.php';

require_once MYSERVICES_PATH . 'admin/functions.php';
require_once XOOPS_ROOT_PATH . '/class/tree.php';
require_once XOOPS_ROOT_PATH . '/class/uploader.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

/**
 * Affiche les détails d'une commande
 */
$id                  = \Xmf\Request::getInt('id', 0, 'GET');
$myservices_Currency = \XoopsModules\Myservices\Currency::getInstance();

xoops_header(false);
echo '<br><br>';
\XoopsModules\Myservices\Utilities::htitle(_AM_MYSERVICES_ORDER . ' : ' . $id, 4);
$commande = null;
$commande = $hMsOrders->get($id);
if (!is_object($commande)) {
    die(_AM_MYSERVICES_ERROR_15);
}

$critePanier = new \Criteria('caddy_orders_id', $id, '=');
$tblCaddy    = [];
$tblCaddy    = $hMsCaddy->getObjects($critePanier);
echo '<br>' . _AM_MYSERVICES_DATE_ORDER . ' : ' .\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($commande->getVar('orders_date')) . '<br><br>';
echo '<b>&raquo;</b> <span style="text-decoration: underline;">' . _AM_MYSERVICES_CLIENT_INFO . '</span>';
echo '<br>' . $commande->getVar('orders_lastname') . ' ' . $commande->getVar('orders_firstname');
echo '<br>' . $commande->getVar('orders_address');
echo '<br>' . $commande->getVar('orders_zip') . ' ' . $commande->getVar('orders_town');
echo '<br>' . $commande->getVar('orders_telephone') . ' (' . $commande->getVar('orders_email') . ')';

echo '<br><br><br><b>&raquo;</b> <u>' . _AM_MYSERVICES_ORDER_INFO . '</u>';
foreach ($tblCaddy as $caddy) {
    $employee = $product = null;
    $employee = $hMsEmployees->get($caddy->getVar('caddy_employees_id'));
    $product  = $hMsProducts->get($caddy->getVar('caddy_products_id'));
    echo '<br>';
    echo\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($caddy->getVar('caddy_start')) . ' - ';
    echo\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($caddy->getVar('caddy_end')) . ' - ';
    echo $product->getVar('products_title') . ' - ';
    echo $employee->getEmployeeFullName() . ' - ';
    echo $myservices_Currency->amountForDisplay($caddy->getVar('caddy_price'));
    echo '<br>';
}
xoops_footer();
