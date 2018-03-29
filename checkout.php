<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

/**
 * Saisie des données du client + affichage des informations saisies pour validation avec redirection vers Paypal
 */
require_once __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'myservices_order.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once MYSERVICES_PATH . 'class/myservices_paypal.php';
require_once MYSERVICES_PATH . 'class/myservices_cart.php';
require_once MYSERVICES_PATH . 'class/registryfile.php';

$op = 'default';
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

$cartForTemplate = [];
$emptyCart       = false;

$commandAmount  = $vatAmount = $commandAmountTTC = 0;
$myservicesCart = &myservices_Cart::getInstance();    // Pour gérer le panier
$vatArray       = [];
$vatArray       = $hMsVat->getItems();

$passwordCancel   = md5(xoops_makepass());
$myservicesPaypal = new myservices_paypal(myservices_utils::getModuleOption('paypal_test'), myservices_utils::getModuleOption('paypal_email'), myservices_utils::getModuleOption('paypal_money'), true, $passwordCancel);
$passwordInvoice  = md5(xoops_makepass());

// Calcul du contenu du caddy
function listCart()
{
    global $xoopsTpl, $myservicesCart, $cartForTemplate, $emptyCart, $commandAmount, $vatAmount, $commandAmountTTC;
    $myservicesCart->computeCart($cartForTemplate, $emptyCart, $commandAmount, $vatAmount, $commandAmountTTC);
}

$tbl_country = XoopsLists::getCountryList();
if (is_object($xoopsUser)) {
    $uid = $xoopsUser->getVar('uid');
} else {
    $uid = 0;
}

$xoopsTpl->assign('moduleName', myservices_utils::getModuleName());

/**
 * Affichage du formulaire de saisie pour valider la commande
 *
 * @param boolean $message Indique s'il faut afficher le message d'erreur ou pas
 */
function CheckoutForm($message = false)
{
    global $xoopsTpl, $uid, $hMsOrders, $commandAmountTTC, $xoopsUser;
    $registry = new myservices_registryfile();
    $cgv      = $registry->getfile(MYSERVICES_TEXTFILE3);

    $notFound = true;
    if ($uid > 0) {    // Si c'est un utlisateur enregistré, on recherche dans les anciennes commandes pour pré remplir les champs
        $tblCommand  = [];
        $critereUser = new \Criteria('orders_uid', $uid, '=');
        $critereUser->setSort('orders_date');
        $critereUser->setOrder('DESC');    // Pour récupérer sa dernière commande
        $critereUser->setLimit(1);
        $tblCommand = $hMsOrders->getObjects($critereUser, false);
        if (count($tblCommand) > 0) {
            $notFound = false;
            $commande = $tblCommand[0];
        }
    }

    if ($notFound) {
        $commande = $hMsOrders->create(true);
        $commande->setVar('orders_country', 'FR');
    }

    $currency = myservices_currency::getInstance();

    $sform = new \XoopsThemeForm(_MYSERVICES_PLEASE_ENTER, 'informationfrm', MYSERVICES_URL . 'checkout.php', 'post', true);
    $sform->addElement(new \XoopsFormHidden('op', 'paypal'));
    $sform->addElement(new \XoopsFormLabel(_MYSERVICES_TOTAL, $currency->amountForDisplay($commandAmountTTC, 'l')));
    $sform->addElement(new \XoopsFormText(_MYSERVICES_LASTNAME, 'orders_lastname', 50, 255, $commande->getVar('orders_lastname')), true);
    $sform->addElement(new \XoopsFormText(_MYSERVICES_FIRSTNAME, 'orders_firstname', 50, 255, $commande->getVar('orders_firstname')), false);
    $sform->addElement(new \XoopsFormTextArea(_MYSERVICES_STREET, 'orders_address', $commande->getVar('orders_address'), 3, 50), true);
    $sform->addElement(new \XoopsFormText(_MYSERVICES_CP, 'orders_zip', 5, 30, $commande->getVar('orders_zip')), true);
    $sform->addElement(new \XoopsFormText(_MYSERVICES_CITY, 'orders_town', 40, 255, $commande->getVar('orders_town')), true);
    $sform->addElement(new \XoopsFormSelectCountry(_MYSERVICES_COUNTRY, 'orders_country', $commande->getVar('orders_country')), true);
    $sform->addElement(new \XoopsFormText(_MYSERVICES_PHONE, 'orders_telephone', 15, 50, $commande->getVar('orders_telephone')), true);
    $sform->addElement(new \XoopsFormText(_MYSERVICES_EMAIL, 'orders_email', 50, 255, $commande->getVar('orders_email')), true);

    $label          = "<a href=\"javascript:openWithSelfMain('" . MYSERVICES_URL . "cgv.php','',640,480);\">" . _MYSERVICES_CGV_ACCEPT . '</a>';
    $yesCGVCheckbox = new \XoopsFormCheckBox(_MYSERVICES_CGV, 'cgv');
    $yesCGVCheckbox->addOption(1, $label);
    $sform->addElement($yesCGVCheckbox, true);

    $button_tray = new \XoopsFormElementTray('', '');
    $submit_btn  = new \XoopsFormButton('', 'btnsubmit', _MYSERVICES_SAVE, 'submit');    // post
    $button_tray->addElement($submit_btn);
    $sform->addElement($button_tray);
    // Marquage des champs obligatoires
    $sform = myservices_utils::formMarkRequiredFields($sform);
    $xoopsTpl->assign('form', $sform->render());
}

switch ($op) {
    // ****************************************************************************************************************
    case 'default':    // Présentation du formulaire
        // ****************************************************************************************************************
        if ($myservicesCart->isCartEmpty()) {
            myservices_utils::redirect(_MYSERVICES_CART_IS_EMPTY, MYSERVICES_URL, 4);
        }
        listCart();
        CheckoutForm();
        break;

    // ****************************************************************************************************************
    case 'paypal':    // Validation finale avant envoi sur Paypal
        // ****************************************************************************************************************
        if ($myservicesCart->isCartEmpty()) {
            myservices_utils::redirect(_MYSERVICES_CART_IS_EMPTY, MYSERVICES_URL, 4);
        }

        listCart();
        // On vérifie que les CGV sont bien acceptées
        if (!isset($_POST['cgv']) || (isset($_POST['cgv']) && 1 != (int)$_POST['cgv'])) {
            myservices_utils::redirect(_MYSERVICES_CGV_ERROR, 'checkout.php', 4);
        }

        // Enregistrement de la commande
        $commande = $hMsOrders->create(true);
        $commande->setVars($_POST);
        $commande->setVar('orders_uid', $uid);
        $commande->setVar('orders_date', date('Y-m-d H:i:s'));
        $commande->setVar('orders_state', MYSERVICES_ORDER_NOINFORMATION);
        $commande->setVar('orders_ip', myservices_utils::IP());
        $commande->setVar('orders_total', $commandAmountTTC);
        $commande->setVar('orders_cancel', $passwordCancel);
        $commande->setVar('orders_articles_count', count($cartForTemplate));
        $commande->setVar('orders_password', $passwordInvoice);
        $res = $hMsOrders->insert($commande, true);
        if (!$res) {    // Si la sauvegarde n'a pas fonctionné
            myservices_utils::redirect(_MYSERVICES_ERROR10, MYSERVICES_URL, 6);
        }

        // Enregistrement du panier
        $msgCommande = '';
        foreach ($cartForTemplate as $line) {
            $caddy = $hMsCaddy->create(true);
            $caddy->setVar('caddy_products_id', $line['id']);
            $caddy->setVar('caddy_employes_id', $line['empid']);
            $caddy->setVar('caddy_calendar_id', 0);
            $caddy->setVar('caddy_orders_id', $commande->getVar('orders_id'));
            $caddy->setVar('caddy_price', $line['products_price_ttc_db']);
            $caddy->setVar('caddy_vat_rate', $line['products_vat_rate']);
            $caddy->setVar('caddy_start', $line['starting_date']);
            $caddy->setVar('caddy_end', $line['ending_date']);
            $res                = $hMsCaddy->insert($caddy, true);
            $elementsCommande   = [];
            $elementsCommande[] = _MYSERVICES_SERVICE . ' : ' . $line['products_title'];
            $elementsCommande[] = _MYSERVICES_STARTING_DATE . ' : ' . myservices_utils::sqlDateTimeToFrench($line['starting_date']);
            $elementsCommande[] = _MYSERVICES_ENDING_DATE . ' : ' . myservices_utils::sqlDateTimeToFrench($line['ending_date']);
            $elementsCommande[] = _MYSERVICES_PRODUCT_PRICETTC . ' : ' . $line['products_price_ttc'];
            $msgCommande        .= implode("\n", $elementsCommande) . "\n";
        }
        $currency    = myservices_currency::getInstance();
        $msgCommande .= "\n" . _MYSERVICES_TOTAL_TTC . ' : ' . $currency->amountForDisplay($commandAmountTTC, 'l') . "\n";

        $msg                 = [];
        $msg['COMMANDE']     = $msgCommande;
        $msg['NUM_COMMANDE'] = $commande->getVar('orders_id');
        $msg['NOM']          = $commande->getVar('orders_lastname');
        $msg['PRENOM']       = $commande->getVar('orders_firstname');
        $msg['ADRESSE']      = $commande->getVar('orders_address');
        $msg['CP']           = $commande->getVar('orders_zip');
        $msg['VILLE']        = $commande->getVar('orders_town');
        $msg['PAYS']         = $tbl_country[$commande->getVar('orders_country')];
        $msg['TELEPHONE']    = $commande->getVar('orders_telephone');
        $msg['EMAIL']        = $commande->getVar('orders_email');
        $msg['IP']           = myservices_utils::IP();

        // Envoi du mail au client
        myservices_utils::sendEmailFromTpl('command_client.tpl', $commande->getVar('orders_email'), sprintf(_MYSERVICES_THANKYOU_CMD, $xoopsConfig['sitename']), $msg);
        // Envoi du mail au groupe de personnes devant recevoir le mail
        myservices_utils::sendEmailFromTpl('command_shop.tpl', myservices_utils::getEmailsFromGroup(myservices_utils::getModuleOption('grp_sold')), _MYSERVICES_NEW_COMMAND, $msg);

        // Présentation du formulaire pour envoi à Paypal
        $payURL = $myservicesPaypal->getURL();

        // Présentation finale avec panier en variables cachées ******************************
        $sform    = new \XoopsThemeForm(_MYSERVICES_PAY_PAYPAL, 'payform', $payURL, 'post', true);
        $elements = [];
        $elements = $myservicesPaypal->getFormContent($commande->getVar('orders_id'), $commandAmountTTC, $commande->getVar('orders_email'));
        foreach ($elements as $key => $value) {
            $sform->addElement(new \XoopsFormHidden($key, $value));
        }

        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_TOTAL_TTC, $currency->amountForDisplay($commandAmountTTC, 'l')));
        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_LASTNAME, $commande->getVar('orders_lastname')));
        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_FIRSTNAME, $commande->getVar('orders_firstname')));
        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_STREET, $commande->getVar('orders_address')));
        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_CP, $commande->getVar('orders_zip')));
        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_CITY, $commande->getVar('orders_town')));
        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_COUNTRY, $tbl_country[$commande->getVar('orders_country')]));
        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_PHONE, $commande->getVar('orders_telephone')));
        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_EMAIL, $commande->getVar('orders_email')));

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'btnsubmit', _MYSERVICES_PAY_PAYPAL, 'submit');    // post
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $xoopsTpl->assign('form', $sform->render());
        $xoopsTpl->assign('op', 'paypal');
        break;
}
$title = _MYSERVICES_VALIDATE_CMD . ' - ' . myservices_utils::getModuleName();
myservices_utils::setMetas($title, $title);
require_once(XOOPS_ROOT_PATH . '/footer.php');
