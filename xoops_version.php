<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

include __DIR__ . '/preloads/autoloader.php';

$modversion['name']           = _MI_MYSERVICES_NAME;
$modversion['version']        = 2.0;
$modversion['description']    = _MI_MYSERVICES_DESC;
$modversion['author']         = 'Hervé Thouzard';
$modversion['help']           = 'page=help';
$modversion['license']        = 'GNU GPL 2.0 or later';
$modversion['license_url']    = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']       = 0;
$modversion['image']          = 'assets/images/logoModule.png';
$modversion['dirname']        = basename(__DIR__);
$modversion['dirmoduleadmin'] = 'Frameworks/moduleclasses/moduleadmin';
$modversion['sysicons16']     = 'Frameworks/moduleclasses/icons/16';
$modversion['sysicons32']     = 'Frameworks/moduleclasses/icons/32';
$modversion['modicons16']     = 'assets/images/icons/16';
$modversion['modicons32']     = 'assets/images/icons/32';
//about
$modversion['module_status']       = 'Beta 1';
$modversion['release_date']        = '2016/07/05';
$modversion['module_website_url']  = 'www.xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.5';
$modversion['min_xoops']           = '2.5.9';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = [
    'mysql'  => '5.0.7',
    'mysqli' => '5.0.7'
];

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][0]        = 'myservices_caddy';
$modversion['tables'][1]        = 'myservices_calendar';
$modversion['tables'][2]        = 'myservices_categories';
$modversion['tables'][3]        = 'myservices_employes';
$modversion['tables'][4]        = 'myservices_employesproducts';
$modversion['tables'][5]        = 'myservices_orders';
$modversion['tables'][6]        = 'myservices_prefs';
$modversion['tables'][7]        = 'myservices_products';
$modversion['tables'][8]        = 'myservices_vat';

$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

//********************************************************************************************************************
// Blocks *************************************************************************************************************
//********************************************************************************************************************
$cptb = 0;

/**
 * Liste des catégories
 */
++$cptb;
$modversion['blocks'][$cptb]['file']        = 'block_myservices_categories.php';
$modversion['blocks'][$cptb]['name']        = _MI_MYSERVICES_BNAME2;
$modversion['blocks'][$cptb]['description'] = '';
$modversion['blocks'][$cptb]['show_func']   = 'b_ms_categories_show';
$modversion['blocks'][$cptb]['edit_func']   = 'b_ms_categories_edit';
$modversion['blocks'][$cptb]['options']     = '1'; // Number of visible elements simultaneously
$modversion['blocks'][$cptb]['template']    = 'myservices_block_categories.tpl';
/**
 * Products List
 */
++$cptb;
$modversion['blocks'][$cptb]['file']        = 'block_myservices_products.php';
$modversion['blocks'][$cptb]['name']        = _MI_MYSERVICES_BNAME3;
$modversion['blocks'][$cptb]['description'] = '';
$modversion['blocks'][$cptb]['show_func']   = 'b_ms_products_show';
$modversion['blocks'][$cptb]['edit_func']   = 'b_ms_products_edit';
$modversion['blocks'][$cptb]['options']     = '1'; // Number of visible elements simultaneously
$modversion['blocks'][$cptb]['template']    = 'myservices_block_products.tpl';
/**
 * Employee List
 */
++$cptb;
$modversion['blocks'][$cptb]['file']        = 'block_myservices_employes.php';
$modversion['blocks'][$cptb]['name']        = _MI_MYSERVICES_BNAME1;
$modversion['blocks'][$cptb]['description'] = '';
$modversion['blocks'][$cptb]['show_func']   = 'b_ms_employes_show';
$modversion['blocks'][$cptb]['edit_func']   = 'b_ms_employes_edit';
$modversion['blocks'][$cptb]['options']     = '1'; // Number of visible items simultaneously
$modversion['blocks'][$cptb]['template']    = 'myservices_block_employes.tpl';
/**
 * Detailed list of categories
 */
++$cptb;
$modversion['blocks'][$cptb]['file']        = 'block_myservices_detcategories.php';
$modversion['blocks'][$cptb]['name']        = _MI_MYSERVICES_BNAME4;
$modversion['blocks'][$cptb]['description'] = '';
$modversion['blocks'][$cptb]['show_func']   = 'b_ms_detcategories_show';
$modversion['blocks'][$cptb]['edit_func']   = '';
$modversion['blocks'][$cptb]['options']     = '';
$modversion['blocks'][$cptb]['template']    = 'myservices_block_detcategories.tpl';
//********************************************************************************************************************
// Menu ***************************************************************************************************************
//********************************************************************************************************************
$modversion['hasMain'] = 1;
$cptm                  = 0;
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_MYSERVICES_SMNAME1;
$modversion['sub'][$cptm]['url']  = 'index.php'; // Categories list
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_MYSERVICES_SMNAME2;
$modversion['sub'][$cptm]['url']  = 'cart.php'; // Basket
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_MYSERVICES_SMNAME3;
$modversion['sub'][$cptm]['url']  = 'employees.php'; // Employees
++$cptm;
$modversion['sub'][$cptm]['name'] = _MI_MYSERVICES_SMNAME4;
$modversion['sub'][$cptm]['url']  = 'products.php'; // Products
// Add the parents categories menu ***************************************** ***************
global $xoopsModule, $hMsCategories;
if (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $modversion['dirname'] && $xoopsModule->getVar('isactive')) {
    if (!isset($hMsCategories)) {
        $hMsCategories = xoops_getModuleHandler('myservices_categories', 'myservices');
    }
    $tblCategories = [];
    $tblCategories = $hMsCategories->getMotherCategories();
    foreach ($tblCategories as $item) {
        ++$cptm;
        $modversion['sub'][$cptm]['name'] = $item->getVar('categories_title');
        $modversion['sub'][$cptm]['url']  = basename($item->getCategoryLink());
    }
}
//********************************************************************************************************************
// Research **********************************************************************************************************
//********************************************************************************************************************
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'myservices_search';
//********************************************************************************************************************
// Comments *******************************************************************************************************
//********************************************************************************************************************
$modversion['hasComments'] = 0;
//********************************************************************************************************************
// Templates **********************************************************************************************************
//********************************************************************************************************************
$cptt = 0;

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_curemployee.tpl';
$modversion['templates'][$cptt]['description'] = '(AJAX) Information on the selected person';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_availability.tpl';
$modversion['templates'][$cptt]['description'] = '(AJAX) Checking the availability of a person to a product, a date and a given period';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_onecategory.tpl';
$modversion['templates'][$cptt]['description'] = 'Information of a category';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_oneproduct.tpl';
$modversion['templates'][$cptt]['description'] = 'Information of a product';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_index.tpl';
$modversion['templates'][$cptt]['description'] = 'List of the main categories (index)';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_category.tpl';
$modversion['templates'][$cptt]['description'] = 'Products of a category and sub-categories';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_cart.tpl';
$modversion['templates'][$cptt]['description'] = 'Basket';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_order.tpl';
$modversion['templates'][$cptt]['description'] = 'control Passage';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_thankyou.tpl';
$modversion['templates'][$cptt]['description'] = 'Thank you for your order';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_cancelpurchase.tpl';
$modversion['templates'][$cptt]['description'] = 'Order canceled';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_product.tpl';
$modversion['templates'][$cptt]['description'] = 'Product Category';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_employes.tpl';
$modversion['templates'][$cptt]['description'] = 'List of employees';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_employe.tpl';
$modversion['templates'][$cptt]['description'] = 'Employee Page';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_cancel.tpl';
$modversion['templates'][$cptt]['description'] = 'Cancelling an order or item of control';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_invoice.tpl';
$modversion['templates'][$cptt]['description'] = 'Online invoice';

++$cptt;
$modversion['templates'][$cptt]['file']        = 'myservices_products.tpl';
$modversion['templates'][$cptt]['description'] = 'List all products';

//********************************************************************************************************************
// Preferences ********************************************************************************************************
//********************************************************************************************************************
$cpto = 0;
/**
 * Editor to use
 */
++$cpto;

// default admin editor
xoops_load('XoopsEditorHandler');
$editorHandler              = XoopsEditorHandler::getInstance();
$editorList                  = array_flip($editorHandler->getList());
$modversion['config'][$cpto] = [
    'name'        => 'form_options',
    'title'       => '_MI_MYSERVICES_FORM_OPTIONS',
    'description' => '_MI_MYSERVICES_FORM_OPTIONS_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'dhtmltextarea',
    'options'     => $editorList
];

/**
 * Currency's sign placement (left or right)?
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'monnaie_place';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF00';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF00_DSC';
$modversion['config'][$cpto]['formtype']    = 'yesno';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 1;
/**
 * Decimals count
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'decimals_count';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF01';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF02_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = '2';
/**
 * Thousands separator
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'thousands_sep';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF02';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF02_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'text';
$modversion['config'][$cpto]['default']     = '';
/**
 * Decimal separator
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'decimal_sep';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF03';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF03_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'text';
$modversion['config'][$cpto]['default']     = ',';
/**
 * Money, full label
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'money_full';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF04';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF04_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'text';
$modversion['config'][$cpto]['default']     = 'euro(s)';
/**
 * Money, short label
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'money_short';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF05';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF05_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'text';
$modversion['config'][$cpto]['default']     = '€';
/**
 * Items count per page
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'perpage';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF06';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF06_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 15;
/**
 * Email address to use for Paypal
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'paypal_email';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF07';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF07_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'text';
$modversion['config'][$cpto]['default']     = '';
/**
 * Paypal money's code
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'paypal_money';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF08';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF08_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'text';
$modversion['config'][$cpto]['default']     = 'EUR';
/**
 * Are you in Paypal test mode?
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'paypal_test';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF09';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF09_DSC';
$modversion['config'][$cpto]['formtype']    = 'yesno';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 1;
/**
 * Who send an email when a cancellation?
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'grp_ordercancel';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF10';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF10_DSC';
$modversion['config'][$cpto]['formtype']    = 'group';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 0;
/**
 * Group of users to send an email wich When a product is sold
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'grp_sold';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF11';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF11_DSC';
$modversion['config'][$cpto]['formtype']    = 'group';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 0;
/**
 * Do you want to use URL rewriting?
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'urlrewriting';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF12';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF12_DSC';
$modversion['config'][$cpto]['formtype']    = 'yesno';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 0;
/**
 * Mime Types
 * Default values: Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'mimetypes';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF13';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF13_DSC';
$modversion['config'][$cpto]['formtype']    = 'textarea';
$modversion['config'][$cpto]['valuetype']   = 'text';
$modversion['config'][$cpto]['default']     = "image/gif\nimage/jpeg\nimage/pjpeg\nimage/x-png\nimage/png\napplication/x-zip-compressed\napplication/zip\napplication/pdf\napplication/x-gtar\napplication/x-tar";
/**
 * Upload Filesize MAX in kilobytes
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'maxuploadsize';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF14';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF14_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 1048576;
/**
 * Maximum time of cancellation in hours
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'maxdelaycancel';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF15';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF15_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 48;
/**
 * Beat time between 2 services
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'battement';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF17';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF17_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 30;
/**
 * Hours before ordering
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'latence';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF18';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF18_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 48;
/**
 * Field separator for export
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'csvsep';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF16';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF16_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'text';
$modversion['config'][$cpto]['default']     = '|';
/**
 * Number of columns categories on the module home page
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'columnscount';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF19';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF19_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 2;
/**
 * Items count per page
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'prodperline';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF20';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF20_DSC';
$modversion['config'][$cpto]['formtype']    = 'textbox';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 3;
/**
 * Send an email to employees to report their reservations?
 */
++$cpto;
$modversion['config'][$cpto]['name']        = 'email_employees';
$modversion['config'][$cpto]['title']       = '_MI_MYSERVICES_CONF21';
$modversion['config'][$cpto]['description'] = '_MI_MYSERVICES_CONF21_DSC';
$modversion['config'][$cpto]['formtype']    = 'yesno';
$modversion['config'][$cpto]['valuetype']   = 'int';
$modversion['config'][$cpto]['default']     = 1;

// ********************************************************************************************************************
// Notifications ******************************************************************************************************
// ********************************************************************************************************************
$modversion['hasNotification'] = 0;
