<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

/**
 * Page appelée par Paypal dans le cas de l'annulation d'une commande
 */
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'myservices_cancelpurchase.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once MYSERVICES_PATH . 'class/myservices_cart.php';

require_once MYSERVICES_PATH . 'class/myservices_paypal.php';
if (isset($_GET['id'])) {
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
                myservices_utils::sendEmailFromTpl('command_shop_cancel.tpl', myservices_utils::getEmailsFromGroup(myservices_utils::getModuleOption('grp_sold')), _MYSERVICES_PAYPAL_CANCELED, $msg);
                myservices_utils::sendEmailFromTpl('command_client_cancel.tpl', $commande->getVar('cmd_email'), _MYSERVICES_PAYPAL_CANCELED, $msg);
            }
        }
        $myservicesCart = new myservices_Cart();    // Pour gérer le panier
        $myservicesCart->emptyCart();
    }
}

$title = _MYSERVICES_PAYPAL_CANCELED . ' - ' . myservices_utils::getModuleName();
myservices_utils::setMetas($title, $title);
require_once(XOOPS_ROOT_PATH . '/footer.php');
