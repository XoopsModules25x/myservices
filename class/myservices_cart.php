<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 * Classe responsable de gérer le panier
 *
 * @package       Myservices
 * @author        Hervé Thouzard - Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 * @todo          Utiliser un registre
 */
class myservices_Cart
{
    const CADDY_NAME = 'myservices_cart';    // Nom du panier en session

    /**
     * Access the only instance of this class
     *
     * @return object
     *
     * @static
     * @staticvar   object
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Calcul du caddy à partir du tableau en session qui se présente sous la forme :
     *    $datas['number']    = indice du produit (de 1 à N)
     *  $datas['id']        = Identifiant du produit
     *    $datas['qty']        = Durée en heures
     *  $datas['empid']    = Identifiant de l'employé(e)
     *  $datas['hour']        = Heure de début
     *  $datas['date']        = Date de réservation (au format YYYY-MM-DD)
     *
     *  Note : Les paramètres entrants de la fonction sont utilisés comme paramètres sortants... (sic)
     *
     * @param array $cartForTemplate  Contenu du caddy à passer au template (en fait la liste des produits)
     * @param       boolean           emptyCart Indique si le panier est vide ou pas
     * @param float $commandAmount    Montant HT de la commande
     * @param float $vatAmount        VAT amount
     * @param float $commandAmountTTC Tax amount of the order
     */
    public function computeCart(&$cartForTemplate, &$emptyCart, &$commandAmount, &$vatAmount, &$commandAmountTTC)
    {
        global $hMsCalendar, $hMsCategories, $hMsEmployes, $hMsEmployesproducts, $hMsProducts, $hMsVat, $hMsPrefs;
        if ($this->isCartEmpty()) {    // Pas de caddie
            $emptyCart = true;
        } else {
            $emptyCart  = false;
            $tblCaddie  = [];
            $tblCaddie  = isset($_SESSION[self::CADDY_NAME]) ? $_SESSION[self::CADDY_NAME] : [];
            $caddyCount = count($tblCaddie);
            if ($caddyCount > 0) {
                $currency = myservices_currency::getInstance();

                foreach ($tblCaddie as $caddyElement) {
                    $datas          = [];
                    $produit_number = $caddyElement['number'];    // Numéro séquentiel
                    $produit_id     = $caddyElement['id'];            // Identifiant Produit
                    $employes_id    = $caddyElement['empid'];        // Identifiant employé(e)
                    $startingHour   = $caddyElement['hour'];        // Heure de début
                    $date           = $caddyElement['date'];                // Date de réservation
                    $produit_qty    = $caddyElement['qty'];        // Durée en heures
                    // On récupère le produit concerné
                    $product = null;
                    $product = $hMsProducts->get($produit_id);
                    if (!is_object($product)) {
                        trigger_error(_MYSERVICES_ERROR3, E_USER_ERROR);

                        return null;
                    }
                    // Puis l'employé(e)
                    $employee = null;
                    $employee = $hMsEmployes->get($employes_id);
                    if (!is_object($employee)) {
                        trigger_error(_MYSERVICES_ERROR11, E_USER_ERROR);

                        return null;
                    }

                    $datas                               = $product->toArray();
                    $datas['id']                         = $produit_id;
                    $datas['empid']                      = $employes_id;
                    $datas['products_reserved_date']     = myservices_utils::SQLDateToHuman($date, 's');
                    $datas['products_reserved_time']     = $startingHour;
                    $datas['products_reserved_duration'] = $produit_qty;
                    $datas['employee']                   = $employee->toArray();
                    $datas['products_number']            = $produit_number;
                    // Données utilisées pour la création de la commande (donc non affichées)
                    $startingTimestamp      = strtotime($date . ' ' . $startingHour);
                    $endingTimestamp        = $startingTimestamp + ($produit_qty * 3600);
                    $datas['starting_date'] = date('Y-m-d H:i:s', $startingTimestamp);
                    $datas['ending_date']   = date('Y-m-d H:i:s', $endingTimestamp);
                    // Calculs "financiers" ***********************************
                    $ht         = (float)$product->getVar('products_price');
                    $prixReelHT = $ht * $produit_qty;
                    $VATrate    = $product->getVATRate();
                    $montantTVA = $product->getVATAmount($caddyElement['qty'], $VATrate);

                    $datas['products_amount_ht']    = $currency->amountForDisplay($prixReelHT);        // Montant HT * Quantit�
                    $datas['products_vat_amount']   = $currency->amountForDisplay($montantTVA);        // VAT amount
                    $datas['products_vat_rate']     = $currency->amountInCurrency($VATrate);            // Taux de TVA
                    $datas['products_price_ttc']    = $currency->amountForDisplay($prixReelHT + $montantTVA, 's');
                    $datas['products_price_ttc_db'] = $prixReelHT + $montantTVA;
                    $cartForTemplate[]              = $datas;

                    // Les cumuls (dans les variables "globales")
                    $commandAmount    += $prixReelHT;                        // Montant cumulé HT
                    $vatAmount        += $montantTVA;                            // Montant cumulé de la TVA
                    $commandAmountTTC += $prixReelHT + $montantTVA;        // Montant cumulé TTC
                }
                // fin des calculs
            }
        }
    }

    /**
     * Mise à jour des quantités du caddy suite à la validation du formulaire du caddy
     */
    public function updateQuantites()
    {
        global $hMsCalendar, $hMsCategories, $hMsEmployes, $hMsEmployesproducts, $hMsProducts, $hMsVat, $hMsPrefs;
        $tbl_caddie = $tbl_caddie2 = [];
        if (isset($_SESSION[self::CADDY_NAME])) {
            $tbl_caddie = $_SESSION[self::CADDY_NAME];
            foreach ($tbl_caddie as $produit) {
                $number = $produit['number'];
                $name   = 'qty_' . $number;
                if (isset($_POST[$name])) {
                    $valeur = (int)$_POST[$name];
                    if ($valeur > 0) {
                        $product_id = $produit['id'];
                        $product    = null;
                        $product    = $hMsProducts->get($product_id);
                        if (is_object($product)) {
                            $produit['qty'] = $valeur;
                            $tbl_caddie2[]  = $produit;
                        }
                    }
                }
            }
            if (count($tbl_caddie2) > 0) {
                $_SESSION[self::CADDY_NAME] = $tbl_caddie2;
            } else {
                unset($_SESSION[self::CADDY_NAME]);
            }
        }
    }

    /**
     * Suppression d'un produit du caddy
     *
     * @param integer $indice Indice de l'élément à supprimer
     */
    public function deleteProduct($indice)
    {
        $tbl_caddie = [];
        if (isset($_SESSION[self::CADDY_NAME])) {
            $tbl_caddie = $_SESSION[self::CADDY_NAME];
            if (isset($tbl_caddie[$indice])) {
                unset($tbl_caddie[$indice]);
                if (count($tbl_caddie) > 0) {
                    $_SESSION[self::CADDY_NAME] = $tbl_caddie;
                } else {
                    unset($_SESSION[self::CADDY_NAME]);
                }
            }
        }
    }

    /**
     * Ajout d'un produit au caddy
     * Note, les produits ajoutés mais déjà présents dans le panier ne sont pas dupliqués, on modifie la quantité
     *
     * @param integer $product_id   Identifiant du produit
     * @param integer $quantity     Quantité en heures
     * @param integer $employees_id Identifiant de l'employé(e)
     * @param string  $hour         Heure de début pour la prestation
     * @param string  $date         Date de la prestation (au format YYYY-MM-DD)
     */
    public function addProduct($product_id, $quantity, $employees_id, $hour, $date)
    {
        $tbl_caddie = $tbl_caddie2 = [];
        if (isset($_SESSION[self::CADDY_NAME])) {
            $tbl_caddie = $_SESSION[self::CADDY_NAME];
        }
        $exists = -1;
        foreach ($tbl_caddie as $key => $produit) {
            if ($produit['id'] == $product_id && $produit['date'] == $date && $produit['hour'] == $hour) {
                $exists = $key;
            }
        }

        $datas = [];
        if ($exists == -1) {
            $datas['number'] = count($tbl_caddie) + 1;    // Rang dans le tableau
        } else {
            $datas['number'] = $exists;    // Rang dans le tableau
        }
        $datas['id']                  = $product_id;
        $datas['qty']                 = $quantity;
        $datas['empid']               = $employees_id;
        $datas['hour']                = $hour;
        $datas['date']                = $date;
        $tbl_caddie[$datas['number']] = $datas;
        $_SESSION[self::CADDY_NAME]   = $tbl_caddie;
    }

    /**
     * Indique si le caddy est vide ou pas
     *
     * @return boolean vide, ou pas...
     */
    public function isCartEmpty()
    {
        if (isset($_SESSION[self::CADDY_NAME])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Vidage du caddy, s'il existe
     */
    public function emptyCart()
    {
        if (isset($_SESSION[self::CADDY_NAME])) {
            unset($_SESSION[self::CADDY_NAME]);
        }
    }
}
