<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

/**
 * Classe responsable de la gestion de tout ce qui est relatif � Paypal
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

class myservices_paypal
{
    public $testMode;
    public $email;
    public $moneyCode;
    public $useIpn;
    public $passwordCancel;

    public function __construct($testMode, $emailPaypal, $moneyCode, $ipn = false, $passwordCancel = '')
    {
        $this->testMode       = $testMode;
        $this->email          = $emailPaypal;
        $this->moneyCode      = $moneyCode;
        $this->useIpn         = $ipn;
        $this->passwordCancel = $passwordCancel;
    }

    /**
     * Renvoie l'url à utiliser en tenant compte du fait qu'on est en mode test ou pas
     * @param bool $securized
     * @return string
     */
    public function getURL($securized = false)
    {
        if (!$securized) {
            if (1 == $this->testMode) {
                return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            } else {
                return 'https://www.paypal.com/cgi-bin/webscr';
            }
        } else {
            if (1 == $this->testMode) {
                return 'www.sandbox.paypal.com';
            } else {
                return 'www.paypal.com';
            }
        }
    }

    /**
     * Formate le montant au format Paypal
     * @param $amount
     * @return string
     */
    private function formatAmount($amount)
    {
        return sprintf('%.02f', $amount);
    }

    /**
     * Renvoie les éléments à ajouter au formulaire en tant que zones cachées
     *
     * @param       $commandId
     * @param float $ttc TTC à facturer
     * @param       $emailClient
     * @return array
     */
    public function getFormContent($commandId, $ttc, $emailClient)
    {
        global $xoopsConfig;
        $ret                     = [];
        $ret['cmd']              = '_xclick';
        $ret['upload']           = '1';
        $ret['currency_code']    = $this->moneyCode;
        $ret['business']         = $this->email;
        $ret['return']           = MYSERVICES_URL . 'thankyou.php';            // Page (générique) de remerciement après paiement
        $ret['image_url']        = XOOPS_URL . '/images/logo.gif';
        $ret['cpp_header_image'] = XOOPS_URL . '/images/logo.gif';
        $ret['invoice']          = $commandId;
        $ret['item_name']        = _MYSERVICES_ORDER . $commandId . ' - ' . $xoopsConfig['sitename'];
        $ret['item_number']      = $commandId;
        $ret['amount']           = $this->formatAmount($ttc);
        $ret['custom']           = $commandId;
        //$ret['rm'] = 2;	// Renvoyer les données par POST (normalement)
        $ret['email'] = $emailClient;
        // paypal_pdt
        if ('' != xoops_trim($this->passwordCancel)) {    // URL à laquelle le navigateur du client est ramené si le paiement est annulé
            $ret['cancel_return'] = MYSERVICES_URL . 'cancel-payment.php?id=' . $this->passwordCancel;
        }
        if (1 == $this->useIpn) {
            $ret['notify_url'] = MYSERVICES_URL . 'paypal-notify.php';
        }

        return $ret;
    }
}
