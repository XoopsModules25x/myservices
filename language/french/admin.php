<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */
define('_AM_MYSERVICES_GO_TO_MODULE', 'Aller au module');
define('_AM_MYSERVICES_PREFERENCES', 'Préférences');
define('_AM_MYSERVICES_ADMINISTRATION', 'Administration');
define('_AM_MYSERVICES_OK', 'Ok');
define('_AM_MYSERVICES_SAVE_OK', 'Données enregistrées avec succès');
define('_AM_MYSERVICES_SAVE_PB', 'Problème durant la sauvegarde des données');
define('_AM_MYSERVICES_ACTION', 'Action');
define('_AM_MYSERVICES_ADD_ITEM', 'Ajouter un élément');
define('_AM_MYSERVICES_CONF_DELITEM', 'Voulez vous vraiment supprimer cet élément ?');
define('_AM_MYSERVICES_LIST', 'Liste');
define('_AM_MYSERVICES_ID', 'Id');
define('_AM_MYSERVICES_CURRENT_PICTURE', 'Image actuelle');
define('_AM_MYSERVICES_PICTURE', 'Image');
define('_AM_MYSERVICES_SELECT_HLP', 'Utilisez la touche Ctrl (ou la touche pomme sur Mac)<br>pour choisir plusieurs éléments');
define('_AM_MYSERVICES_DUPLICATED', 'Dupliqué');
define('_AM_MYSERVICES_CONF_DEL_CATEG', 'Confirmez-vous la suppression de la catégorie suivante ainsi que ses sous catégories ? :<br>%s');
define('_AM_MYSERVICES_SORRY_NOREMOVE', "Désolé, il n'est pas possible de supprimer ce produit car il fait partie des commandes suivantes");

define('_AM_MYSERVICES_ERROR_1', "Erreur, pas d'identifiant spécifié");
define('_AM_MYSERVICES_ERROR_2', 'Erreur, impossible de supprimer cette TVA, elle est utilisée par un produit');
define('_AM_MYSERVICES_ERROR_3', 'Erreur, aucun utilisateur valide spécifié');
define('_AM_MYSERVICES_ERROR_4', 'Erreur, impossible de supprimer cette catégorie (ou ses sous-catégories) car elle est utilisée par des produits');
define('_AM_MYSERVICES_ERROR_5', "Erreur, impossible de supprimer cette personne, elle fait partie d'offres de services dans les commandes");
define('_AM_MYSERVICES_ERROR_6', 'Erreur, la catégorie ne peut pas avoir pour catégorie mère elle même !');
define('_AM_MYSERVICES_ERROR_7', "Erreur, impossible de créer le fichier d'export");
define('_AM_MYSERVICES_ERROR_8', "Erreur, impossible de créer des produits tant qu'il n'y a pas de catégories");
define('_AM_MYSERVICES_ERROR_9', "Erreur, impossible de créer des produits tant qu'il n'y a pas de TVA");
define('_AM_MYSERVICES_ERROR_10', "Erreur, impossible de créer des produits tant qu'il n'y a pas d'employés");
define('_AM_MYSERVICES_ERROR_11', "Erreur, impossible de mettre une personne en congé tant qu'il n'y a pas d'employés");
define('_AM_MYSERVICES_ERROR_12', 'Erreur, statut indéfini !');
define('_AM_MYSERVICES_ERROR_13', 'Erreur, impossible de trouver les préférences du magasin');
define('_AM_MYSERVICES_ERROR_14', 'Erreur, catégorie inconnue');
define('_AM_MYSERVICES_ERROR_15', 'Erreur, commande inconnue');
define('_AM_MYSERVICES_NOT_FOUND', 'Erreur, élément introuvable');

define('_AM_MYSERVICES_CLOSE_SHOP', 'Fermetures exceptionnelles du magasin');
define('_AM_MYSERVICES_MODIFY', 'Modifier');
define('_AM_MYSERVICES_ADD', 'Ajouter');
define('_AM_MYSERVICES_WORK_HOURS', 'Horaires de travail');
define('_AM_MYSERVICES_TIMESHEET_HLP', 'Indiquez les horaires de travail du magasin, jour par jour');

define('_AM_MYSERVICES_FROM', 'De : ');
define('_AM_MYSERVICES_TO', 'à : ');

define('_AM_MYSERVICES_ADD_VAT', ' Ajouter une TVA');
define('_AM_MYSERVICES_EDIT_VAT', "Edition d'une TVA");

define('_AM_MYSERVICES_ADD_CLOSE', ' Ajouter une fermeture');
define('_AM_MYSERVICES_EDIT_CLOSE', "Edition d'une fermeture");

define('_AM_MYSERVICES_ADD_EMPL', 'Ajouter un(e) employé(e)');
define('_AM_MYSERVICES_EDIT_EMPL', "Edition d'un(e) employé(e)");

define('_AM_MYSERVICES_ADD_CATEG', 'Ajouter une catégorie');
define('_AM_MYSERVICES_EDIT_CATEG', 'Editer une catégorie');

define('_AM_MYSERVICES_EDIT_HOLIDAY', 'Edition des congés/absences');
define('_AM_MYSERVICES_ADD_HOLIDAY', 'Ajout de congés/absences');

define('_AM_MYSERVICES_CATEG_TITLE', 'Titre');
define('_AM_MYSERVICES_PARENT_CATEG', 'Catégorie mère');
define('_AM_MYSERVICES_DESCRIPTION', ' Description');
define('_MI_MYSERVICES_ADVERTISEMENT', 'Publicité');

define('_AM_MYSERVICES_EDIT_PRODUCT', 'Editer un produit');
define('_AM_MYSERVICES_ADD_PRODUCT', 'Ajouter un produit');
define('_AM_MYSERVICES_INDEX_PAGE', "Page d'index du module");
define('_AM_MYSERVICES_PERIMED', 'Adhésions arrivant à terme');
define('_AM_MYSERVICES_ADD_USER', 'Ajouter un utilisateur à une adhésion');
define('_AM_MYSERVICES_USER_ID', "Numéro de l'utilisateur");
define('_AM_MYSERVICES_PRODUCT', 'Produit');
define('_AM_MYSERVICES_LIMIT_TO', 'Filtre');
define('_AM_MYSERVICES_FILTER', 'Filtrer');
define('_AM_MYSERVICES_CONF_VALIDATE', 'Confirmez-vous la validation de cette commande ?');
define('_AM_MYSERVICES_CSV_EXPORT', 'Export au format CSV');
define('_AM_MYSERVICES_DATE', 'Date');
define('_AM_MYSERVICES_CLIENT', 'Client');
define('_AM_MYSERVICES_CSV_READY', "Votre fichier CSV est prêt pour le téléchargement, cliquez sur ce lien pour l'obtenir");
define('_AM_MYSERVICES_CANCEL', "Texte explicatif à joindre aux emails<br>de commande pour expliquer la<br>procédure d'annulation");
define('_AM_MYSERVICES_CGV', 'Conditions Générales de Vente');
define('_AM_MYSERVICES_PHOTOSDESC', 'Veuillez prendre des photos de taille identique');

define('_AM_MYSERVICES_STATE', 'Etat');
define('_AM_MYSERVICES_PRODUCT_PRICE_HT', 'Prix HT <span style="text-decoration: underline;">UNITAIRE</span> (et horaire)');
define('_AM_MYSERVICES_QUALITY', 'Texte à inclure pour demander aux client de remplir le formulaire de qualité');
define('_AM_MYSERVICES_LAST_ORDERS', 'Dernières commandes validées');
define('_AM_MYSERVICES_DETAILS_ORDERS', 'Dernières commandes validées');
define('_AM_MYSERVICES_CLIENT_INFO', 'Informations sur le client');
define('_AM_MYSERVICES_ORDER_INFO', 'Informations sur la commande');
define('_AM_MYSERVICES_DATE_ORDER', 'Date de la commande');
define('_AM_MYSERVICES_ORDER', 'Commande');
