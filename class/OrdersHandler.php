<?php

namespace XoopsModules\Myservices;

/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

//require_once XOOPS_ROOT_PATH.'/kernel/object.php';
//if (!class_exists('myservices_ORM')) {
//    require_once XOOPS_ROOT_PATH . '/modules/myservices/class/PersistableObjectHandler.php';
//}

define('MYSERVICES_ORDER_NOINFORMATION', 0);    // Pas encore d'informations sur la commande
define('MYSERVICES_ORDER_VALIDATED', 1);        //  Commande validée par Paypal
define('MYSERVICES_ORDER_PENDING', 2);            // En attente
define('MYSERVICES_ORDER_FAILED', 3);            // Echec
define('MYSERVICES_ORDER_CANCELED', 4);            // Annulée
define('MYSERVICES_ORDER_FRAUD', 5);            // Fraude

/**
 * Class OrdersHandler
 * @package XoopsModules\Myservices
 */
class OrdersHandler extends Myservices\ServiceORM
{
    /**
     * OrdersHandler constructor.
     * @param $db
     */
    public function __construct($db)
    {    //                         Table                   Classe              Id
        parent::__construct($db, 'myservices_orders', Orders::class, 'orders_id');
    }

    /**
     * Validation d'une commande (changement d'état)
     *
     * @param  int  $cmd_id       Identifiant de la commande
     * @param array $qualityLinks Lien vers les formulaires de qualité
     * @return array Tableau contenant les textes à envoyer par email à la personne (les textes des produits)
     */
    public function validateOrder($cmd_id, &$qualityLinks)
    {
        global $hMsCaddy, $hMsCalendar, $hMsEmployees, $hMsProducts, $hMsVat, $hMsPrefs;
        $cmd_id   = (int)$cmd_id;
        $retval   = $elementsCommande = [];
        $commande = null;
        $commande = $this->get($cmd_id);
        if (is_object($commande)) {
            $commande->setVar('orders_state', MYSERVICES_ORDER_VALIDATED);
            $this->insert($commande, true);
        }
        $elementsCommande[] = _MYSERVICES_CLIENT_INFO;
        $elementsCommande[] = $commande->getVar('orders_firstname') . ' ' . $commande->getVar('orders_lastname');
        $elementsCommande[] = $commande->getVar('orders_address');
        $elementsCommande[] = $commande->getVar('orders_zip') . ' ' . $commande->getVar('orders_town');
        $elementsCommande[] = $commande->getVar('orders_telephone');

        // Récupération des services réservés
        $tblServices = [];
        $tblServices = $hMsCaddy->getObjects(new \Criteria('caddy_orders_id', $cmd_id, '='));
        foreach ($tblServices as $service) {    // Boucle sur les éléments du panier
            $employee = $product = null;
            $employee = $hMsEmployees->get($service->getVar('caddy_employees_id'));
            $product  = $hMsProducts->get($service->getVar('caddy_products_id'));

            $calendar = $hMsCalendar->create(true);
            $calendar->setVar('calendar_status', CALENDAR_STATUS_WORK);
            $calendar->setVar('calendar_employees_id', $service->getVar('caddy_employees_id'));
            $calendar->setVar('calendar_start', $service->getVar('caddy_start'));
            $calendar->setVar('calendar_end', $service->getVar('caddy_end'));
            $calendar->setVar('calendar_products_id', $service->getVar('caddy_products_id'));
            $res = $hMsCalendar->insert($calendar, true);
            if ($res) {    // Mise à jour du caddy (lien caddy <-> calendar)
                $service->setVar('caddy_calendar_id', $calendar->getVar('calendar_id'));
                $hMsCaddy->insert($service, true);
            }

            // Liens vers les formulaires de qualité
            $qualityLinks[] = $product->getVar('products_quality_link');
            // Doit on prévenir les salariés ?
            if (1 == \XoopsModules\Myservices\Utilities::getModuleOption('email_employees') && '' != xoops_trim($employee->getVar('employees_email'))) {
                $recipients   = $msg = $elementsService = [];
                $recipients   = \XoopsModules\Myservices\Utilities::getEmailsFromGroup(\XoopsModules\Myservices\Utility::getModuleOption('grp_sold'));    // Copie aux responsables du site
                $recipients[] = $employee->getVar('employees_email'); // Plus le (la) salarié(e)

                $elementsService[] = _MYSERVICES_SERVICE . ' : ' . $product->getVar('products_title');
                $elementsService[] = _MYSERVICES_STARTING_DATE . ' : ' . \XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($service->getVar('caddy_start'));
                $elementsService[] = _MYSERVICES_ENDING_DATE . ' : ' . \XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($service->getVar('caddy_end'));

                $msg['DETAIL'] = implode("\n", $elementsCommande) . "\n\n" . implode("\n", $elementsService);
                $msg['DETAIL'] = \XoopsModules\Myservices\Utilities::textForEmail($msg['DETAIL']);
                \XoopsModules\Myservices\Utilities::sendEmailFromTpl('employee_service.tpl', $recipients, _MYSERVICES_ALERT, $msg);
            }
            $retval[] = _MYSERVICES_SERVICE . ' : ' . $product->getVar('products_title');
            $retval[] = _MYSERVICES_STARTING_DATE . ' : ' . \XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($service->getVar('caddy_start'));
            $retval[] = _MYSERVICES_ENDING_DATE . ' : ' . \XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($service->getVar('caddy_end'));
            $retval[] = _MYSERVICES_PRODUCT_PRICETTC . ' : ' . $service->getVar('caddy_price');
            $retval[] = "\n";
        }

        return $retval;
    }
}
