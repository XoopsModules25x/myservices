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
 * Page appelée par Paypal dans le cas de l'annulation d'une commande
 */
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'myservices_cancelpurchase.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
// require_once MYSERVICES_PATH . 'class/myservices_cart.php';

// require_once MYSERVICES_PATH . 'class/Paypal.php';
if (\Xmf\Request::hasVar('id', 'GET')) {
    $critere = new \Criteria('orders_cancel', $myts->addSlashes($_GET['id']), '=');
    $cnt     = 0;
    $tblCmd  = [];
    $cnt     = $hMsOrders->getCount($critere);
    if ($cnt > 0) {
        $tblCmd = $hMsOrders->getObjects($critere);
        if (count($tblCmd) > 0) {
            $commande = null;
            $commande = $tblCmd[0];
            if (is_object($commande)) {
                $commande->setVar('orders_state', MYSERVICES_ORDER_CANCELED);
                $hMsOrders->insert($commande, true);
                $msg                 = [];
                $msg['NUM_COMMANDE'] = $commande->getVar('orders_id');
                \XoopsModules\Myservices\Utilities::sendEmailFromTpl('command_shop_cancel.tpl', \XoopsModules\Myservices\Utilities::getEmailsFromGroup(\XoopsModules\Myservices\Utilities::getModuleOption('grp_sold')), _MYSERVICES_PAYPAL_CANCELED, $msg);
                \XoopsModules\Myservices\Utilities::sendEmailFromTpl('command_client_cancel.tpl', $commande->getVar('cmd_email'), _MYSERVICES_PAYPAL_CANCELED, $msg);
            }
        }
        $myservicesCart = new Myservices\Cart();    // Pour gérer le panier
        $myservicesCart->emptyCart();
    }
}

$title = _MYSERVICES_PAYPAL_CANCELED . ' - ' . \XoopsModules\Myservices\Utilities::getModuleName();
\XoopsModules\Myservices\Utilities::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';
