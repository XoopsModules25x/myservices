<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

define('_MYSERVICES_MODIFY', 'Modifier');
define('_MYSERVICES_EDIT', 'Editer');
define('_MYSERVICES_BTN_VALIDATE', 'Valider');
define('_MYSERVICES_BTN_UNVALIDATE', 'Dévalider');

define('_MYSERVICES_PRODUCT', 'Produit');
define('_MYSERVICES_RATE', 'Taux');
define('_MYSERVICES_NOTIN_HOURS', 'Texte pour expliquer comment<br>commander hors période de travail');
define('_MYSERVICES_EMPLOYE', 'Employé(e)');
define('_MYSERVICES_UNKNOW_EMPLOYE', 'Employé(e) iconnue');
define('_MYSERVICES_STARTING_DATE', 'Date de début');
define('_MYSERVICES_ENDING_DATE', 'Date de fin');
define('_MYSERVICES_ISACTIVE', 'Disponible');
define('_MYSERVICES_DURATION', 'Durée minimale en heures');
define('_MYSERVICES_EMPLOYES_IN_HOLIDAYS', 'Personnes en vacances ou réservations faites en dehors du site');
define('_MYSERVICES_ONLINE', 'En ligne');
define('_MYSERVICES_PRODUCT_ID', 'Identifiant');
define('_MYSERVICES_PRODUCT_TITLE', 'Titre');
define('_MYSERVICES_PRODUCT_CATEGORY', 'Catégorie');
define('_MYSERVICES_PRODUCT_DESC', 'Description');
define('_MYSERVICES_PRODUCT_QUALITY', 'Lien vers le formulaire de qualité');
define('_MYSERVICES_PRODUCT_EMPLOYES', 'Personnes qui proposent ce service');
define('_MYSERVICES_PRODUCT_SUMMARY', 'Sommaire');
define('_MYSERVICES_PRODUCT_PRICE_HT', 'Prix HT');
define('_MYSERVICES_PRODUCT_PRICE_UNIT', 'Prix HT unitaire');
define('_MYSERVICES_PRODUCT_PRICETTC', 'Prix TTC');
define('_MYSERVICES_PRODUCT_VAT', 'Taux de TVA');
define('_MYSERVICES_DELETE', 'Supprimer');
define('_MYSERVICES_ADD', 'Ajouter');
define('_MYSERVICES_SAVE', 'Enregistrer');
define('_MYSERVICES_FIRSTNAME', 'Prénom');
define('_MYSERVICES_LASTNAME', 'Nom');
define('_MYSERVICES_EMAIL', 'Email');
define('_MYSERVICES_BIO', 'Renseignements sur la personne');
define('_MYSERVICES_CURRENT_PICTURE', 'Image courante');
define('_MYSERVICES_CHANGE_PICTURE', 'Image à utiliser');

define('_MYSERVICES_DUPLICATE_PRODUCT', 'Dupliquer le produit');

define('_MYSERVICES_READ_MORE', 'En savoir plus ...');
define('_MYSERVICES_EMPTY_ITEM_SURE', 'Souhaitez vous vraiment supprimer cet élément ?');
define('_MYSERVICES_UPDATE', 'Recalculer');
define('_MYSERVICES_STREET', 'Adresse');
define('_MYSERVICES_CP', 'Code Postal');
define('_MYSERVICES_CITY', 'Ville');
define('_MYSERVICES_COUNTRY', 'Pays');
define('_MYSERVICES_PHONE', 'Numéro de téléphone');
define('_MYSERVICES_DETAILS', 'Détails');
define('_MYSERVICES_ADRESSE', 'Adresse');
define('_MYSERVICES_VILLE', 'Ville');
define('_MYSERVICES_TELEPHONE', 'Téléphone');

define('_MYSERVICES_LISTE', 'Liste des produits');
define('_MYSERVICES_YOUR_LIST', 'Liste de vos produits');
define('_MYSERVICES_BUY', 'Acheter');

define('_MYSERVICES_ERROR1', "Erreur, aucune catégorie n'a été spécifiée");
define('_MYSERVICES_ERROR2', 'Erreur, catégorie inconnue');
define('_MYSERVICES_ERROR3', 'Erreur, produit inconnu');
define('_MYSERVICES_ERROR4', 'Erreur, produit non spécifié');
define('_MYSERVICES_ERROR5', 'Erreur, la catégorie du produit est introuvable');
define('_MYSERVICES_ERROR6', "Erreur, ce produit n'est pas disponible");
define('_MYSERVICES_ERROR7', "Aucune personne n'assure ce service");
define('_MYSERVICES_ERROR8', "Erreur, vous devez spécifier le nom d'une personne");
define('_MYSERVICES_ERROR9', 'Erreur, pas de produit spécifié ou produit introuvable');
define('_MYSERVICES_ERROR10', "Erreur, nous sommes désolés mais nous n'avons pas pu traiter votre commande, veuillez nous contacter");
define('_MYSERVICES_ERROR11', 'Erreur, employé(e)inconnue');
define('_MYSERVICES_ERROR12', "Erreur, cette personne n'est plus active");

define('_MYSERVICES_ORDER', 'Commande ');	// For paypal
define('_MYSERVICES_THANK_YOU', 'Merci pour votre paiement avec Paypal, votre commande sera traitée dans les plus brefs délais.');
define('_MYSERVICES_TRANSACTION_FINSIHED', 'Votre transaction est terminée');
define('_MYSERVICES_CONTINUE_SHOPPING', 'Vous pouvez continuer votre visite sur notre site');
define('_MYSERVICES_PURCHASE_FINSISHED', 'Commande terminée');
define('_MYSERVICES_VALIDATE_CMD', "Valider l'achat");

define('_MYSERVICES_PAYPAL_NOINFO', 'Commande non traitée par Paypal');
define('_MYSERVICES_PAYPAL_CANCELED', 'Commande annulée');
define('_MYSERVICES_PAYPAL_VALIDATED', 'Commande validée par Paypal');
define('_MYSERVICES_PAYPAL_FRAUD', 'Commande frauduleuse');
define('_MYSERVICES_PAYPAL_PENDING', 'Commande en attente');
define('_MYSERVICES_PAYPAL_FAILED', 'Commande échouée');

define('_MYSERVICES_EMPTY_CART_SURE', 'Voulez vous vraiment vider votre panier ?');
define('_MYSERVICES_CART_IS_EMPTY', 'Votre panier est vide');
define('_MYSERVICES_REMOVE_ITEM', "Supprimer l'élément ?");
define('_MYSERVICES_QUANTITY', 'Quantité');
define('_MYSERVICES_TOTAL', 'Total');
define('_MYSERVICES_TOTAL_TTC', 'Total TTC');
define('_MYSERVICES_QTY_MODIFIED', 'Avez vous modifié une quantité ?');
define('_MYSERVICES_EMPTY_CART', 'Vider le panier');
define('_MYSERVICES_GO_ON', 'Continuer vos achats');
define('_MYSERVICES_CHECKOUT', 'Passer commande');
define('_MYSERVICES_PLEASE_ENTER', 'Veuillez saisir les informations suivantes pour valider votre commande');
define('_MYSERVICES_DETAILS_EMAIL', 'Vous recevrez les détails de votre commande par email');
define('_MYSERVICES_REQUIRED', '* Champs requis');
define('_MYSERVICES_THANKYOU_CMD', 'Merci pour votre commande sur le site %s');
define('_MYSERVICES_NEW_COMMAND', 'Nouvelle commande sur le site');
define('_MYSERVICES_PAY_PAYPAL', 'Payer');
define('_MYSERVICES_CMD_STATE', 'Etat de la commande');

define('_MYSERVICES_ORDER_CANCEL', 'Votre commande a été annulée');

define('_MYSERVICES_CMD_STATE1', 'Commandes non traitées par Paypal');
define('_MYSERVICES_CMD_STATE2', 'Commandes validées');
define('_MYSERVICES_CMD_STATE3', 'Commandes en attente');
define('_MYSERVICES_CMD_STATE4', 'Commandes échouées');
define('_MYSERVICES_CMD_STATE5', 'Commandes annulées');
define('_MYSERVICES_CMD_STATE6', 'Commandes frauduleuses');
define('_MYSERVICES_VALIDATE_COMMAND', 'Valider la commande');
define('_MYSERVICES_SEE_PRODUCTS', 'Voir les produits de la catégorie');
define('_MYSERVICES_SEE_PRODUCT', 'Voir le produit');
define('_MYSERVICES_CATEGORIES', 'Catégories de services');
define('_MYSERVICES_PRODUCTSOF_CATEGORY', 'Produits de cette catégorie');

define('_MYSERVICES_STATE_WORK', 'Travail');
define('_MYSERVICES_STATE_HOLIDAY', 'Congés');
define('_MYSERVICES_STATE_CLOSED', 'Fermé');
define('_MYSERVICES_STATE_UNDEFINED', 'Non défini');
define('_MYSERVICES_SORRY_NOPROD', "Désolé il n'y a pas de produits dans cette catégorie");

define('_MYSERVICES_SERVICES_BY_ME', 'Liste des services que fournit ');
define('_MYSERVICES_SEE_LIST_EMPLOYEES', 'Voir la liste de tous les employés');
define('_MYSERVICES_SEE_ALL_PRODUCTS', 'Voir la liste de tous les produits');
define('_MYSERVICES_EMPLOYEES_LIST', 'Liste des employés');
define('_MYSERVICES_EMPLOYEE', "Choisissez l'intervenant(e)");
define('_MYSERVICES_DATE', 'Choissisez la date');
define('_MYSERVICES_SELECT_DAY', 'Choissisez un jour');
define('_MYSERVICES_NO_EMPLOYEES', "Désolé mais nous n'avons pas encore d'employés");
define('_MYSERVICES_OUT_OF_PERIOD', 'Comment réserver hors période ?');
define('_MYSERVICES_MORE_ABOUT', 'En savoir plus sur cette personne');
define('_MYSERVICES_STARTING_HOUR', 'Heure de début');
define('_MYSERVICES_DURATION_SEL', 'Durée en heures');
define('_MYSERVICES_RESERVE', 'Réserver');
define('_MYSERVICES_VERIFY_AVAILABILITY', 'Vérifier la disponibilité');

define('_MYSERVICES_ERROR13', 'Erreur, vous n avez pas sélectionné de date');
define('_MYSERVICES_ERROR14', 'Erreur, un paramètre est manquant (date ou employé(e)) ou produit');
define('_MYSERVICES_ERROR15', "Le magasin n'est pas ouvert à cette heure, veuillez choisir un autre créneau horaire");
define('_MYSERVICES_ERROR16', "Exceptionnellement, le magasin n'est pas ouvert à cette date, veuillez choisir une autre date");
define('_MYSERVICES_ERROR17', 'Pour commande un service, il faut un délai de %d heures entre maintenant et la date de votre réservation');
define('_MYSERVICES_ERROR18', "Réservation impossible, cette personne est en vacances ou travaille déjà ou l'heure de fin est en dehors des horaires de travail ou la durée de la prestation ne correspond pas avec les horaires de travail.");
define('_MYSERVICES_ERROR19', "Erreur, aucune durée n'a été spécifiée");
define('_MYSERVICES_ERROR20', "Erreur, aucune heure de début n'a été spécifiée");
define('_MYSERVICES_ERROR21', "Erreur, la date de la prestation n'a été spécifiée");
define('_MYSERVICES_AVAILABILITY_OK', 'Cette personne est disponible pour ce jour et pour cette durée');
define('_MYSERVICES_NO_COMMAND_BEFORE', " Il n'est pas possible de passer commande avant %d heures");
define('_MYSERVICES_DATE_DURATION', 'Date, durée<br />et intervenant(e)');
define('_MYSERVICES_AMOUNT_HT', 'Montant HT');
define('_MYSERVICES_VAT', 'TVA');
define('_MYSERVICES_HOURS', 'heure(s)');
define('_MYSERVICES_CGV', 'Conditions Générales de Vente');
define('_MYSERVICES_CGV_ACCEPT', "J'ai lu et j'accepte les conditions générales de vente");
define('_MYSERVICES_CGV_ERROR', 'Pour finaliser votre commande, vous devez accepter les Conditions Générales de Vente');
define('_MYSERVICES_SERVICE', 'Service ');
define('_MYSERVICES_CANCEL_DURATION', 'Vous disposez de %d heures pour annuler votre commande');
define('_MYSERVICES_ALERT', 'Alerte de service');
define('_MYSERVICES_CLIENT_INFO', 'Informations client');
define('_MYSERVICES_RESERVER_PICTURE', 'reserverfr.jpg');
