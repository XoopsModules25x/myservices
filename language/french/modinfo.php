<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id$
 * ****************************************************************************
 */

define("_MI_MYSERVICES_NAME","Services à la maison");
define("_MI_MYSERVICES_DESC","Permet de proposer les services de personnes (par exemple du ménage)");

// Blocs
define("_MI_MYSERVICES_BNAME1","Liste des salariés");
define("_MI_MYSERVICES_BNAME2","Liste des catégories");
define("_MI_MYSERVICES_BNAME3","Liste des produits");
define("_MI_MYSERVICES_BNAME4","Liste détaillée des catégories");

// Sous menus
define("_MI_MYSERVICES_SMNAME1","Catégories");
define("_MI_MYSERVICES_SMNAME2","Panier");
define("_MI_MYSERVICES_SMNAME3","Employés");
define("_MI_MYSERVICES_SMNAME4","Produits");

// Menu Admin
define("_MI_MYSERVICES_ADMENU0","Index");
define("_MI_MYSERVICES_ADMENU1","TVA");
define("_MI_MYSERVICES_ADMENU2","Employés");
define("_MI_MYSERVICES_ADMENU3","Congés/Absences");
define("_MI_MYSERVICES_ADMENU4","Catégories");
define("_MI_MYSERVICES_ADMENU5","Produits");
define("_MI_MYSERVICES_ADMENU6","Fichiers Attachés");
define("_MI_MYSERVICES_ADMENU7","Commandes");
define("_MI_MYSERVICES_ADMENU8","Textes");
define("_MI_MYSERVICES_ADMENU9","Horaires");

// Options de configuration
define('_MI_MYSERVICES_CONF00',"Emplacement de la monnaie");
define('_MI_MYSERVICES_CONF00_DSC', "Oui = A droite, Non = A gauche");

define('_MI_MYSERVICES_CONF01',"Nombre de décimales");
define('_MI_MYSERVICES_CONF01_DSC', "");

define('_MI_MYSERVICES_CONF02', "Séparateur des milliers");
define('_MI_MYSERVICES_CONF02_DSC', "");

define('_MI_MYSERVICES_CONF03', "Séparateur des décimales");
define('_MI_MYSERVICES_CONF03_DSC', "");

define('_MI_MYSERVICES_CONF04', "Libellé long de la monnaie");
define('_MI_MYSERVICES_CONF04_DSC', "");

define('_MI_MYSERVICES_CONF05', "Libellé court de la monnaie");
define('_MI_MYSERVICES_CONF05_DSC', "");

define('_MI_MYSERVICES_CONF06', "Nombre d'éléments par page");
define('_MI_MYSERVICES_CONF06_DSC', "");

define('_MI_MYSERVICES_CONF07', "Adresse email Paypal");
define('_MI_MYSERVICES_CONF07_DSC', "");

define('_MI_MYSERVICES_CONF08', "Code de la monnaiepour Paypal");
define('_MI_MYSERVICES_CONF08_DSC', "");

define('_MI_MYSERVICES_CONF09', "Paypal en mode test ?");
define('_MI_MYSERVICES_CONF09_DSC', "");

define('_MI_MYSERVICES_CONF10', "Groupe à qui envoyer un email lorsqu'une commande est annulée");
define('_MI_MYSERVICES_CONF10_DSC', "");

define('_MI_MYSERVICES_CONF11', "Groupe à qui envoyer un email lorsqu'une commande est passée");
define('_MI_MYSERVICES_CONF11_DSC', "Attention, ca n'est pas quand une commande est payée");

define('_MI_MYSERVICES_CONF12', "Voulez-vous utiliser l'URL Rewriting ?");
define('_MI_MYSERVICES_CONF12_DSC', "");

define('_MI_MYSERVICES_CONF13', "Types Mime");
define('_MI_MYSERVICES_CONF13_DSC', "Pour attacher des fichiers ou des images");

define('_MI_MYSERVICES_CONF14', "Taille maximale des fichiers téléchargés");
define('_MI_MYSERVICES_CONF14_DSC', "en octets");

define('_MI_MYSERVICES_CONF15', "Délai maximum, en heures, pour annuler une commande");
define('_MI_MYSERVICES_CONF15_DSC', "");

define('_MI_MYSERVICES_CONF16', "Séparateur de champs à utiliser pour les fichiers CSV");
define('_MI_MYSERVICES_CONF16_DSC', "");

define('_MI_MYSERVICES_CONF17', "Temps de battement entre 2 prestations");
define('_MI_MYSERVICES_CONF17_DSC', "En minutes");

define('_MI_MYSERVICES_CONF18', "Nombre d'heures avant de pouvoir passer commande");
define('_MI_MYSERVICES_CONF18_DSC', "");

define('_MI_MYSERVICES_CONF19', "Nombre de colonnes de catégories sur la page d'accueil");
define('_MI_MYSERVICES_CONF19_DSC', "");

define('_MI_MYSERVICES_CONF20', "Nombre de colonnes de produits à afficher sur la page d'une catégorie");
define('_MI_MYSERVICES_CONF20_DSC', "");

define('_MI_MYSERVICES_CONF21', "Prévenir les employé(e)s par email lorsqu'une commande est validée ?");
define('_MI_MYSERVICES_CONF21_DSC', "Afin de leur indiquer la date et le nom et l'adresse du client");

define("_MI_MYSERVICES_FORM_OPTIONS","Option de formulaire");
define('_MI_MYSERVICES_FORM_OPTIONS_DESC', "S&eacute;lectionnez l'éditeur à utiliser. Si vous avez une installation 'simple' (i.e vous utilisez seulement l'&eacute;diteur xoops fourni en standard), alors vous ne pouvez que s&eacute;lectionner DHTML et Compact");
define("_MI_MYSERVICES_FORM_COMPACT","Compact");
define("_MI_MYSERVICES_FORM_DHTML","DHTML");
define("_MI_MYSERVICES_FORM_SPAW","Spaw Editor");
define("_MI_MYSERVICES_FORM_HTMLAREA","HtmlArea Editor");
define("_MI_MYSERVICES_FORM_FCK","FCK Editor");
define("_MI_MYSERVICES_FORM_KOIVI","Koivi Editor");
define("_MI_MYSERVICES_FORM_TINYEDITOR","TinyEditor");
?>