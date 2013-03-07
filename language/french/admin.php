<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id$
 * ****************************************************************************
 */

define("_AM_MYSERVICES_GO_TO_MODULE","Aller au module");
define("_AM_MYSERVICES_PREFERENCES","Pr�f�rences");
define("_AM_MYSERVICES_ADMINISTRATION","Administration");
define("_AM_MYSERVICES_OK","Ok");
define("_AM_MYSERVICES_SAVE_OK","Donn�es enregistr�es avec succ�s");
define("_AM_MYSERVICES_SAVE_PB","Probl�me durant la sauvegarde des donn�es");
define("_AM_MYSERVICES_ACTION", "Action");
define("_AM_MYSERVICES_ADD_ITEM","Ajouter un �l�ment");
define("_AM_MYSERVICES_CONF_DELITEM","Voulez vous vraiment supprimer cet �l�ment ?");
define("_AM_MYSERVICES_LIST","Liste");
define("_AM_MYSERVICES_ID","Id");
define("_AM_MYSERVICES_CURRENT_PICTURE","Image actuelle");
define("_AM_MYSERVICES_PICTURE", "Image");
define("_AM_MYSERVICES_SELECT_HLP", "Utilisez la touche Ctrl (ou la touche pomme sur Mac)<br />pour choisir plusieurs �l�ments");
define("_AM_MYSERVICES_DUPLICATED", "Dupliqu�");
define("_AM_MYSERVICES_CONF_DEL_CATEG", "Confirmez-vous la suppression de la cat�gorie suivante ainsi que ses sous cat�gories ? :<br />%s");
define("_AM_MYSERVICES_SORRY_NOREMOVE", "D�sol�, il n'est pas possible de supprimer ce produit car il fait partie des commandes suivantes");

define("_AM_MYSERVICES_ERROR_1","Erreur, pas d'identifiant sp�cifi�");
define("_AM_MYSERVICES_ERROR_2","Erreur, impossible de supprimer cette TVA, elle est utilis�e par un produit");
define("_AM_MYSERVICES_ERROR_3","Erreur, aucun utilisateur valide sp�cifi�");
define("_AM_MYSERVICES_ERROR_4","Erreur, impossible de supprimer cette cat�gorie (ou ses sous-cat�gories) car elle est utilis�e par des produits");
define("_AM_MYSERVICES_ERROR_5","Erreur, impossible de supprimer cette personne, elle fait partie d'offres de services dans les commandes");
define("_AM_MYSERVICES_ERROR_6","Erreur, la cat�gorie ne peut pas avoir pour cat�gorie m�re elle m�me !");
define("_AM_MYSERVICES_ERROR_7","Erreur, impossible de cr�er le fichier d'export");
define("_AM_MYSERVICES_ERROR_8","Erreur, impossible de cr�er des produits tant qu'il n'y a pas de cat�gories");
define("_AM_MYSERVICES_ERROR_9","Erreur, impossible de cr�er des produits tant qu'il n'y a pas de TVA");
define("_AM_MYSERVICES_ERROR_10","Erreur, impossible de cr�er des produits tant qu'il n'y a pas d'employ�s");
define("_AM_MYSERVICES_ERROR_11","Erreur, impossible de mettre une personne en cong� tant qu'il n'y a pas d'employ�s");
define("_AM_MYSERVICES_ERROR_12","Erreur, statut ind�fini !");
define("_AM_MYSERVICES_ERROR_13","Erreur, impossible de trouver les pr�f�rences du magasin");
define("_AM_MYSERVICES_ERROR_14","Erreur, cat�gorie inconnue");
define("_AM_MYSERVICES_ERROR_15","Erreur, commande inconnue");
define("_AM_MYSERVICES_NOT_FOUND", "Erreur, �l�ment introuvable");

define("_AM_MYSERVICES_CLOSE_SHOP", "Fermetures exceptionnelles du magasin");
define("_AM_MYSERVICES_MODIFY", "Modifier");
define("_AM_MYSERVICES_ADD", "Ajouter");
define("_AM_MYSERVICES_WORK_HOURS", "Horaires de travail");
define("_AM_MYSERVICES_TIMESHEET_HLP", "Indiquez les horaires de travail du magasin, jour par jour");

define("_AM_MYSERVICES_FROM", "De : ");
define("_AM_MYSERVICES_TO", "� : ");

define("_AM_MYSERVICES_ADD_VAT"," Ajouter une TVA");
define("_AM_MYSERVICES_EDIT_VAT", "Edition d'une TVA");

define("_AM_MYSERVICES_ADD_CLOSE"," Ajouter une fermeture");
define("_AM_MYSERVICES_EDIT_CLOSE", "Edition d'une fermeture");

define("_AM_MYSERVICES_ADD_EMPL", "Ajouter un(e) employ�(e)");
define("_AM_MYSERVICES_EDIT_EMPL", "Edition d'un(e) employ�(e)");

define("_AM_MYSERVICES_ADD_CATEG", "Ajouter une cat�gorie");
define("_AM_MYSERVICES_EDIT_CATEG", "Editer une cat�gorie");

define("_AM_MYSERVICES_EDIT_HOLIDAY", "Edition des cong�s/absences");
define("_AM_MYSERVICES_ADD_HOLIDAY", "Ajout de cong�s/absences");

define("_AM_MYSERVICES_CATEG_TITLE", "Titre");
define("_AM_MYSERVICES_PARENT_CATEG","Cat�gorie m�re");
define("_AM_MYSERVICES_DESCRIPTION"," Description");
define("_MI_MYSERVICES_ADVERTISEMENT", "Publicit�");

define("_AM_MYSERVICES_EDIT_PRODUCT", "Editer un produit");
define("_AM_MYSERVICES_ADD_PRODUCT", "Ajouter un produit");
define("_AM_MYSERVICES_INDEX_PAGE", "Page d'index du module");
define("_AM_MYSERVICES_PERIMED", "Adh�sions arrivant � terme");
define("_AM_MYSERVICES_ADD_USER", "Ajouter un utilisateur � une adh�sion");
define("_AM_MYSERVICES_USER_ID", "Num�ro de l'utilisateur");
define("_AM_MYSERVICES_PRODUCT", "Produit");
define("_AM_MYSERVICES_LIMIT_TO", "Filtre");
define("_AM_MYSERVICES_FILTER", "Filtrer");
define('_AM_MYSERVICES_CONF_VALIDATE', "Confirmez-vous la validation de cette commande ?");
define('_AM_MYSERVICES_CSV_EXPORT', "Export au format CSV");
define("_AM_MYSERVICES_DATE", "Date");
define("_AM_MYSERVICES_CLIENT", "Client");
define('_AM_MYSERVICES_CSV_READY', "Votre fichier CSV est pr�t pour le t�l�chargement, cliquez sur ce lien pour l'obtenir");
define('_AM_MYSERVICES_CANCEL', "Texte explicatif � joindre aux emails<br />de commande pour expliquer la<br />proc�dure d'annulation");
define('_AM_MYSERVICES_CGV', "Conditions G�n�rales de Vente");
define('_AM_MYSERVICES_PHOTOSDESC', "Veuillez prendre des photos de taille identique");

define('_AM_MYSERVICES_STATE', "Etat");
define('_AM_MYSERVICES_PRODUCT_PRICE_HT', "Prix HT <u>UNITAIRE</u> (et horaire)");
define('_AM_MYSERVICES_QUALITY', "Texte � inclure pour demander aux client de remplir le formulaire de qualit�");
define('_AM_MYSERVICES_LAST_ORDERS', "Derni�res commandes valid�es");
define('_AM_MYSERVICES_DETAILS_ORDERS', "Derni�res commandes valid�es");
define('_AM_MYSERVICES_CLIENT_INFO', "Informations sur le client");
define('_AM_MYSERVICES_ORDER_INFO', "Informations sur la commande");
define('_AM_MYSERVICES_DATE_ORDER', "Date de la commande");
define('_AM_MYSERVICES_ORDER', "Commande");
?>