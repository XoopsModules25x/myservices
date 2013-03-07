<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id: xoops_version.php 26 2007-12-20 13:25:03Z hthouzard $
 * ****************************************************************************
 */

if (!defined('XOOPS_ROOT_PATH')) {
	die("XOOPS root path not defined");
}

$modversion['name'] = _MI_MYSERVICES_NAME;
$modversion['version'] = 1.0;
$modversion['description'] = _MI_MYSERVICES_DESC;
$modversion['author'] = "Hervé Thouzard - Instant Zero (http://www.instant-zero.com)";
$modversion['help'] = '';
$modversion['license'] = 'Commercial';
$modversion['official'] = 0;
$modversion['image'] = 'images/myservices_logo.png';
$modversion['dirname'] = 'myservices';

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][0] = 'myservices_caddy';
$modversion['tables'][1] = 'myservices_calendar';
$modversion['tables'][2] = 'myservices_categories';
$modversion['tables'][3] = 'myservices_employes';
$modversion['tables'][4] = 'myservices_employesproducts';
$modversion['tables'][5] = 'myservices_orders';
$modversion['tables'][6] = 'myservices_products';
$modversion['tables'][7] = 'myservices_vat';


$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// ********************************************************************************************************************
// Blocks *************************************************************************************************************
// ********************************************************************************************************************
$cptb = 0;

/**
 * Liste des catégories
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'block_myservices_categories.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYSERVICES_BNAME2;
$modversion['blocks'][$cptb]['description'] = '';
$modversion['blocks'][$cptb]['show_func'] = 'b_ms_categories_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_ms_categories_edit';
$modversion['blocks'][$cptb]['options'] = '1';	// Nombre d'éléments visibles simultanément
$modversion['blocks'][$cptb]['template'] = 'myservices_block_categories.html';

/**
 * Liste des produits
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'block_myservices_products.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYSERVICES_BNAME3;
$modversion['blocks'][$cptb]['description'] = '';
$modversion['blocks'][$cptb]['show_func'] = 'b_ms_products_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_ms_products_edit';
$modversion['blocks'][$cptb]['options'] = '1';	// Nombre d'éléments visibles simultanément
$modversion['blocks'][$cptb]['template'] = 'myservices_block_products.html';

/**
 * Liste des salariés
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'block_myservices_employes.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYSERVICES_BNAME1;
$modversion['blocks'][$cptb]['description'] = '';
$modversion['blocks'][$cptb]['show_func'] = 'b_ms_employes_show';
$modversion['blocks'][$cptb]['edit_func'] = 'b_ms_employes_edit';
$modversion['blocks'][$cptb]['options'] = '1';	// nombre d'éléments visibles simultanément
$modversion['blocks'][$cptb]['template'] = 'myservices_block_employes.html';

/**
 * Liste détaillée des catégories
 */
$cptb++;
$modversion['blocks'][$cptb]['file'] = 'block_myservices_detcategories.php';
$modversion['blocks'][$cptb]['name'] = _MI_MYSERVICES_BNAME4;
$modversion['blocks'][$cptb]['description'] = '';
$modversion['blocks'][$cptb]['show_func'] = 'b_ms_detcategories_show';
$modversion['blocks'][$cptb]['edit_func'] = '';
$modversion['blocks'][$cptb]['options'] = '';
$modversion['blocks'][$cptb]['template'] = 'myservices_block_detcategories.html';


// ********************************************************************************************************************
// Menu ***************************************************************************************************************
// ********************************************************************************************************************
$modversion['hasMain'] = 1;
$cptm = 0;

$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_MYSERVICES_SMNAME1;
$modversion['sub'][$cptm]['url'] = 'index.php';		// Liste des catégories

$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_MYSERVICES_SMNAME2;
$modversion['sub'][$cptm]['url'] = 'cart.php';	// Panier

$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_MYSERVICES_SMNAME3;
$modversion['sub'][$cptm]['url'] = 'employees.php';			// Employés

$cptm++;
$modversion['sub'][$cptm]['name'] = _MI_MYSERVICES_SMNAME4;
$modversion['sub'][$cptm]['url'] = 'products.php';			// Produits

// Ajout des catégories mères en sous menu ********************************************************
global $xoopsModule, $hMsCategories;
if (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $modversion['dirname'] && $xoopsModule->getVar('isactive')) {
	if(!isset($hMsCategories)) {
		$hMsCategories = xoops_getmodulehandler('myservices_categories', 'myservices');
	}
	$tblCategories = array();
	$tblCategories = $hMsCategories->getMotherCategories();
	foreach($tblCategories as $item) {
		$cptm++;
		$modversion['sub'][$cptm]['name'] = $item->getVar('categories_title');
		$modversion['sub'][$cptm]['url'] = basename($item->getCategoryLink());
	}
}


// ********************************************************************************************************************
// Recherche **********************************************************************************************************
// ********************************************************************************************************************
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'myservices_search';

// ********************************************************************************************************************
// Commentaires *******************************************************************************************************
// ********************************************************************************************************************
$modversion['hasComments'] = 0;


// ********************************************************************************************************************
// Templates **********************************************************************************************************
// ********************************************************************************************************************
$cptt = 0;

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_curemployee.html';
$modversion['templates'][$cptt]['description'] = "(AJAX) Informations sur la personne sélectionnée";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_availability.html';
$modversion['templates'][$cptt]['description'] = "(AJAX) Vérification de la disponibilité d'une personne pour un produit, une date et une durée donnés";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_onecategory.html';
$modversion['templates'][$cptt]['description'] = "Informations d'une catégorie";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_oneproduct.html';
$modversion['templates'][$cptt]['description'] = "Informations d'un produit";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_index.html';
$modversion['templates'][$cptt]['description'] = "Liste des catégories principales (index)";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_category.html';
$modversion['templates'][$cptt]['description'] = "Produits d'une catégorie et sous-catégories";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_cart.html';
$modversion['templates'][$cptt]['description'] = "Panier";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_order.html';
$modversion['templates'][$cptt]['description'] = "Passage de commande";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_thankyou.html';
$modversion['templates'][$cptt]['description'] = "Merci pour votre commande";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_cancelpurchase.html';
$modversion['templates'][$cptt]['description'] = "Commande annulée";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_product.html';
$modversion['templates'][$cptt]['description'] = "Produit d'une catégorie";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_employes.html';
$modversion['templates'][$cptt]['description'] = "Liste des employés";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_employe.html';
$modversion['templates'][$cptt]['description'] = "Page d'un employé";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_cancel.html';
$modversion['templates'][$cptt]['description'] = "Annulation d'une commande ou d'un élément de la commande";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_invoice.html';
$modversion['templates'][$cptt]['description'] = "Facture en ligne";

$cptt++;
$modversion['templates'][$cptt]['file'] = 'myservices_products.html';
$modversion['templates'][$cptt]['description'] = "Liste de tous les produits";

// ********************************************************************************************************************
// Préférences ********************************************************************************************************
// ********************************************************************************************************************
$cpto = 0;

/**
 * Editor to use
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'form_options';
$modversion['config'][$cpto]['title'] = "_MI_MYSERVICES_FORM_OPTIONS";
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_FORM_OPTIONS_DESC';
$modversion['config'][$cpto]['formtype'] = 'select';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['options'] = array(
											_MI_MYSERVICES_FORM_DHTML=>'dhtml',
											_MI_MYSERVICES_FORM_COMPACT=>'textarea',
											_MI_MYSERVICES_FORM_SPAW=>'spaw',
											_MI_MYSERVICES_FORM_HTMLAREA=>'htmlarea',
											_MI_MYSERVICES_FORM_KOIVI=>'koivi',
											_MI_MYSERVICES_FORM_FCK=>'fck',
											_MI_MYSERVICES_FORM_TINYEDITOR=>'tinyeditor'
											);
$modversion['config'][$cpto]['default'] = 'dhtml';

/**
 * Monnaie's place (left or right) ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'monnaie_place';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF00';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF00_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;


/**
 * Decimals count
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'decimals_count';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF01';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF02_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = '2';

/**
 * Thousands separator
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'thousands_sep';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF02';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF02_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = ' ';

/**
 * Decimal separator
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'decimal_sep';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF03';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF03_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = ',';

/**
 * Money, full label
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'money_full';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF04';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF04_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'euro(s)';

/**
 * Money, short label
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'money_short';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF05';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF05_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '€';

/**
 * Items count per page
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'perpage';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF06';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF06_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 15;

/**
 * Email address to use for Paypal
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'paypal_email';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF07';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF07_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '';

/**
 * Paypal money's code
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'paypal_money';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF08';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF08_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = 'EUR';

/**
 * Are you in Paypal test mode ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'paypal_test';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF09';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF09_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;

/**
 * A qui envoyer un email lors d'une annulation ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'grp_ordercancel';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF10';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF10_DSC';
$modversion['config'][$cpto]['formtype'] = 'group';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;


/**
 * Group of users to wich send an email when a product is sold
 */
$cpto++;
$modversion['config'][$cpto]['name']= 'grp_sold';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF11';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF11_DSC';
$modversion['config'][$cpto]['formtype'] = 'group';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;


/**
 * Do you want to use URL rewriting ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'urlrewriting';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF12';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF12_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 0;


/**
 * Mime Types
 * Default values : Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'mimetypes';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF13';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF13_DSC';
$modversion['config'][$cpto]['formtype'] = 'textarea';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = "image/gif\nimage/jpeg\nimage/pjpeg\nimage/x-png\nimage/png\napplication/x-zip-compressed\napplication/zip\napplication/pdf\napplication/x-gtar\napplication/x-tar";

/**
 * MAX Filesize Upload in kilo bytes
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'maxuploadsize';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF14';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF14_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1048576;

/**
 * Délais maximum d'annulation en heures
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'maxdelaycancel';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF15';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF15_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 48;


/**
 * Temps de battement entre 2 prestations
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'battement';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF17';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF17_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 30;

/**
 * Nombre d'heures avant de pouvoir passer commande
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'latence';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF18';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF18_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 48;

/**
 * Séparateur de champs pour les exports
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'csvsep';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF16';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF16_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'text';
$modversion['config'][$cpto]['default'] = '|';


/**
 * Nombre de colonnes de catégories sur la page d'accueil du module
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'columnscount';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF19';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF19_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 2;

/**
 * Items count per page
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'prodperline';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF20';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF20_DSC';
$modversion['config'][$cpto]['formtype'] = 'textbox';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 3;

/**
 * Envoyer un email aux salariés pour leur signaler les réservations ?
 */
$cpto++;
$modversion['config'][$cpto]['name'] = 'email_employees';
$modversion['config'][$cpto]['title'] = '_MI_MYSERVICES_CONF21';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF21_DSC';
$modversion['config'][$cpto]['formtype'] = 'yesno';
$modversion['config'][$cpto]['valuetype'] = 'int';
$modversion['config'][$cpto]['default'] = 1;



// ********************************************************************************************************************
// Notifications ******************************************************************************************************
// ********************************************************************************************************************
$modversion['hasNotification'] = 0;
?>