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
 * Page appelée par Paypal dans le cas de l'utilisation de l'IPN
 */
require_once __DIR__ . '/header.php';
// require_once MYSERVICES_PATH . 'class/Paypal.php';
// require_once MYSERVICES_PATH . 'class/RegistryFile.php';
@error_reporting(0);

$log     = '';
$req     = 'cmd=_notify-validate';
$slashes = get_magic_quotes_gpc();
foreach ($_POST as $key => $value) {
    if ($slashes) {
        $log   .= "$key=" . stripslashes($value) . "\n";
        $value = urlencode(stripslashes($value));
    } else {
        $log   .= "$key=" . $value . "\n";
        $value = urlencode($value);
    }
    $req .= "&$key=$value";
}
$msg    = [];
$paypal = new Myservices\Paypal(\XoopsModules\Myservices\Utilities::getModuleOption('paypal_test'), \XoopsModules\Myservices\Utilities::getModuleOption('paypal_email'), \XoopsModules\Myservices\Utilities::getModuleOption('paypal_money'), true);
$url    = $paypal->getURL(true);
$header = '';
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= 'Content-Length: ' . mb_strlen($req) . "\r\n\r\n";
$errno  = 0;
$errstr = '';
$fp     = fsockopen($url, 80, $errno, $errstr, 30);
if ($fp) {
    fwrite($fp, "$header$req");
    while (!feof($fp)) {
        $res = fgets($fp, 1024);
        if (0 == strcmp($res, 'VERIFIED')) {
            $log      .= "VERIFIED\t";
            $paypalok = true;
            if ('COMPLETED' !== mb_strtoupper($_POST['payment_status'])) {
                $paypalok = false;
            }
            if (mb_strtoupper($_POST['receiver_email']) != mb_strtoupper(\XoopsModules\Myservices\Utility::getModuleOption('paypal_email'))) {
                $paypalok = false;
            }
            if (mb_strtoupper($_POST['mc_currency']) != mb_strtoupper(\XoopsModules\Myservices\Utility::getModuleOption('paypal_money'))) {
                $paypalok = false;
            }
            if (!$_POST['custom']) {
                $paypalok = false;
            }
            $montant = $_POST['mc_gross'];
            if ($paypalok) {
                $ref      = \Xmf\Request::getInt('custom', 0, 'POST');    // Num�ro de la commande
                $commande = null;
                $commande = $hMsOrders->get($ref);
                if (is_object($commande)) {
                    $msg['NUM_COMMANDE'] = $ref;
                    if ($montant == $commande->getVar('orders_total')) {    //  Commande vérifiée
                        $registry = new Myservices\RegistryFile();
                        $texts    = $qualityLinks = [];
                        $texts    = $hMsOrders->validateOrder($ref, $qualityLinks);    // Validation de la commande, renvoie les informations de la commande
                        if (count($texts) > 0) {
                            $msg['SUPPLEMENTAL'] = implode("\n", $texts);
                        } else {
                            $msg['SUPPLEMENTAL'] = '';
                        }
                        $msg['ANNULATION'] = $registry->getfile(MYSERVICES_TEXTFILE1) . "\n\n" . sprintf(_MYSERVICES_CANCEL_DURATION, \XoopsModules\Myservices\Utilities::getModuleOption('maxdelaycancel'));
                        $msg['QUALITY']    = $registry->getfile(MYSERVICES_TEXTFILE4) . "\n" . implode("\n", $qualityLinks);

                        $msg['SUPPLEMENTAL'] = \XoopsModules\Myservices\Utilities::textForEmail($msg['SUPPLEMENTAL']);
                        $msg['ANNULATION']   = \XoopsModules\Myservices\Utilities::textForEmail($msg['ANNULATION']);
                        $msg['QUALITY']      = \XoopsModules\Myservices\Utilities::textForEmail($msg['QUALITY']);

                        \XoopsModules\Myservices\Utilities::sendEmailFromTpl('command_shop_verified.tpl', \XoopsModules\Myservices\Utilities::getEmailsFromGroup(\XoopsModules\Myservices\Utilities::getModuleOption('grp_sold')), _MYSERVICES_PAYPAL_VALIDATED, $msg);
                        \XoopsModules\Myservices\Utilities::sendEmailFromTpl('command_client_verified.tpl', $commande->getVar('orders_email'), _MYSERVICES_PAYPAL_VALIDATED, $msg);
                    } else {
                        $commande->setVar('orders_state', MYSERVICES_ORDER_FRAUD);
                        $hMsOrders->insert($commande, true);
                        \XoopsModules\Myservices\Utilities::sendEmailFromTpl('command_shop_fraud.tpl', \XoopsModules\Myservices\Utilities::getEmailsFromGroup(\XoopsModules\Myservices\Utilities::getModuleOption('grp_sold')), _MYSERVICES_PAYPAL_FRAUD, $msg);
                    }
                }
            } else {
                if (\Xmf\Request::hasVar('custom', 'POST')) {
                    $ref                 = \Xmf\Request::getInt('custom', 0, 'POST');
                    $msg['NUM_COMMANDE'] = $ref;
                    $commande            = null;
                    $commande            = $hMsOrders->get($ref);
                    if (is_object($commande)) {
                        switch (mb_strtoupper($_POST['payment_status'])) {
                            case 'PENDING':
                                $commande->setVar('orders_state', MYSERVICES_ORDER_PENDING);    // En attente
                                $hMsOrders->insert($commande, true);
                                \XoopsModules\Myservices\Utilities::sendEmailFromTpl('command_shop_pending.tpl', \XoopsModules\Myservices\Utilities::getEmailsFromGroup(\XoopsModules\Myservices\Utility::getModuleOption('grp_sold')), _MYSERVICES_PAYPAL_PENDING, $msg);
                                break;
                            case 'FAILED':
                                $commande->setVar('orders_state', MYSERVICES_ORDER_FAILED);    // Echec
                                $hMsOrders->insert($commande, true);
                                \XoopsModules\Myservices\Utilities::sendEmailFromTpl('command_shop_failed.tpl', \XoopsModules\Myservices\Utilities::getEmailsFromGroup(\XoopsModules\Myservices\Utility::getModuleOption('grp_sold')), _MYSERVICES_PAYPAL_FAILED, $msg);
                                break;
                        }
                    }
                }
            }
        } else {
            $log .= "$res\n";
        }
    }
    fclose($fp);
} else {
    $log .= "Error with the fsockopen function, unable to open communication ' : ($errno) $errstr\n";
}
$fp = fopen(XOOPS_UPLOAD_PATH . '/logpaypal_myservice.txt', 'a');
if ($fp) {
    fwrite($fp, str_repeat('-', 120) . "\n");
    fwrite($fp, date('d/m/Y H:i:s') . "\n");
    if (\Xmf\Request::hasVar('txn_id', 'POST')) {
        fwrite($fp, 'Transaction : ' . $_POST['txn_id'] . "\n");
    }
    fwrite($fp, 'Result : ' . $log . "\n");
    fclose($fp);
}
