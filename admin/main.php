<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

use XoopsModules\Myservices;

require_once __DIR__ . '/admin_header.php';
require_once  dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require_once  dirname(__DIR__) . '/include/common.php';

require_once MYSERVICES_PATH . 'admin/functions.php';
require_once XOOPS_ROOT_PATH . '/class/tree.php';
require_once XOOPS_ROOT_PATH . '/class/uploader.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$op    = \Xmf\Request::getCmd('op', 'default');

$destname = '';
$s        =\XoopsModules\Myservices\Utilities::getModuleOption('csvsep');

/**
 * Function responsible for managing the upload
 *
 * @param integer $indice The index of the file to download
 * @return bool
 */
function myservices_upload($indice)
{
    global $destname;
    if (\Xmf\Request::hasVar('xoops_upload_file', 'POST')) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';
        $fldname = $_FILES[$_POST['xoops_upload_file'][$indice]];
        $fldname = get_magic_quotes_gpc() ? stripslashes($fldname['name']) : $fldname['name'];
        if (xoops_trim('' != $fldname)) {
            $dstpath        = XOOPS_UPLOAD_PATH;
            $destname       =\XoopsModules\Myservices\Utilities::createUploadName($dstpath, $fldname, true);
            $permittedtypes = explode("\n", str_replace("\r", '',\XoopsModules\Myservices\Utilities::getModuleOption('mimetypes')));
            array_walk($permittedtypes, 'trim');
            $uploader = new \XoopsMediaUploader($dstpath, $permittedtypes,\XoopsModules\Myservices\Utilities::getModuleOption('maxuploadsize'));
            $uploader->setTargetFileName($destname);
            if ($uploader->fetchMedia($_POST['xoops_upload_file'][$indice])) {
                if ($uploader->upload()) {
                    return true;
                } else {
                    echo _AM_MYSHOP_ERROR_3 . htmlentities($uploader->getErrors(), ENT_QUOTES | ENT_HTML5);
                }
            } else {
                echo htmlentities($uploader->getErrors(), ENT_QUOTES | ENT_HTML5);
            }
        }
    }

    return false;
}

/**
 * Add a period of one day (eg "from" or "to")
 * @param $libelle
 * @param $de
 * @param $jour
 * @param $indice
 * @param $debutfin
 * @return XoopsFormElementTray
 */
function OneTimeSelect($libelle, $de, $jour, $indice, $debutfin)
{
    $per_tray = new \XoopsFormElementTray('', '');
    $debfin   = 'debut';
    if (2 == $debutfin) {
        $debfin = 'fin';
    }
    $nom1 = sprintf('j%dt%d%s1', $jour + 1, $indice, $debfin);
    $nom2 = sprintf('j%dt%d%s2', $jour + 1, $indice, $debfin);

    $heures1 = new \XoopsFormSelect($libelle, $nom1, (int)substr($de, 0, 2));        // Ajout des heures
    for ($i = 0; $i <= 23; ++$i) {
        $heures1->addOption($i, $i);
    }
    $per_tray->addElement($heures1);
    $minutes1 = new \XoopsFormSelect(' : ', $nom2, (int)substr($de, 3, 2));        // Ajout des minutes
    for ($i = 0; $i <= 60; ++$i) {
        $minutes1->addOption($i, $i);
    }
    $per_tray->addElement($minutes1);

    return $per_tray;
}

/**
 * Adds a full day (with 2 periods)
 * @param $jour
 * @param $de1
 * @param $a1
 * @param $de2
 * @param $a2
 * @param $sform
 */
function OneDay($jour, $de1, $a1, $de2, $a2, &$sform)
{
    global $jours;
    $day_tray = new \XoopsFormElementTray($jours[$jour], '');        // The full tray of day
    // First Period
    $per1_tray = new \XoopsFormElementTray('', '');        // The tray of a full period
    $per1_tray->addElement(OneTimeSelect(_AM_MYSERVICES_FROM, $de1, $jour, 1, 1));
    $per1_tray->addElement(OneTimeSelect(_AM_MYSERVICES_TO, $a1, $jour, 1, 2));
    $day_tray->addElement($per1_tray);
    $day_tray->addElement(new \XoopsFormLabel('', '<br>'));

    // Second Period
    $per2_tray = new \XoopsFormElementTray('', '');        // The tray of a full period
    $per2_tray->addElement(OneTimeSelect(_AM_MYSERVICES_FROM, $de2, $jour, 2, 1));
    $per2_tray->addElement(OneTimeSelect(_AM_MYSERVICES_TO, $a2, $jour, 2, 2));
    $day_tray->addElement($per2_tray);
    $day_tray->addElement(new \XoopsFormLabel('', '<br>'));
    $sform->addElement($day_tray);
}

/**
 * Display the footer of the administration
 */
function show_footer()
{
    echo "<br><br><div align='center'><a href='http://www.instant-zero.com' target='_blank' title='Instant Zero'><img src='../images/instantzero.gif' alt='Instant Zero'></a></div>";
}

// Read some application settings ********************************************************************
$limit               =\XoopsModules\Myservices\Utilities::getModuleOption('perpage');    // Maximum number of items to display in the admin
$baseurl             = MYSERVICES_URL . 'admin/' . basename(__FILE__);    // of this script
$conf_msg            =\XoopsModules\Myservices\Utilities::javascriptLinkConfirm(_AM_MYSERVICES_CONF_DELITEM);
$myservices_Currency = \XoopsModules\Myservices\Currency::getInstance();

global $xoopsConfig;
/** @var Myservices\Helper $helper */
$helper = Myservices\Helper::getInstance();
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');


// ******************************************************************************************************************************************
// **** Main ********************************************************************************************************************************
// ******************************************************************************************************************************************
switch ($op) {

    // ****************************************************************************************************************
    case 'vat':    // Managing VAT
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(1);
        $start  = \Xmf\Request::getInt('start', 0, 'GET');
        $tblVat = [];
        $form   = "<form method='post' action='$baseurl' name='frmaddvat' id='frmaddvat'><input type='hidden' name='op' id='op' value='addvat'><input type='submit' name='btngo' id='btngo' value='" . _AM_MYSERVICES_ADD_ITEM . "'></form>";
        echo $form;
       \XoopsModules\Myservices\Utilities::htitle(_MI_MYSERVICES_ADMENU1, 4);
        $tblVat = $hMsVat->getItems($start, $limit);
        $class  = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYSERVICES_ID . "</th><th align='center'>" . _MYSERVICES_RATE . "</th><th align='center'>" . _AM_MYSERVICES_ACTION . '</th></tr>';
        foreach ($tblVat as $item) {
            $class         = ('even' === $class) ? 'odd' : 'even';
            $action_edit   = "<a href='$baseurl?op=editvat&id=" . $item->getVar('vat_id') . "' title='" . _MYSERVICES_EDIT . "'>" . $icones['edit'] . '</a>';
            $action_delete = "<a href='$baseurl?op=deletevat&id=" . $item->getVar('vat_id') . "' title='" . _MYSERVICES_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" . $item->getVar('vat_id') . "</td><td align='right'>" . $myservices_Currency->amountInCurrency($item->getVar('vat_rate')) . "</td><td align='center'>" . $action_edit . ' ' . $action_delete . "</td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='3' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'saveeditvat':    // Save a VAT
        // ****************************************************************************************************************
        xoops_cp_header();
        $id         = \Xmf\Request::getInt('vat_id', 0, 'POST');
        $opRedirect = 'vat';
        if (!empty($id)) {
            $edit = true;
            $item = $hMsVat->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $hMsVat->create(true);
        }

        $item->setVars($_POST);
        $res = $hMsVat->insert($item);
        if ($res) {
           \XoopsModules\Myservices\Utilities::updateCache();
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'deletevat':    // Delete VAT
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'vat';
        // On v�rifie que cette TVA n'est pas utilis�e par un produit
        $criteria = new \Criteria('products_vat_id', $id, '=');
        $cnt      = $hMsProducts->getCount($criteria);
        if (0 == $cnt) {
            $item = null;
            $item = $hMsVat->get($id);
            if (is_object($item)) {
                $res = $hMsVat->delete($item, true);
                if ($res) {
                   \XoopsModules\Myservices\Utilities::updateCache();
                   \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
                } else {
                   \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
                }
            } else {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_2, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'addvat':    // Add VAT
    case 'editvat':    // Edit VAT
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(1);
        $object = 'vat';
        if ($op == 'edit' . $object) {
            $title = _AM_MYSERVICES_EDIT_VAT;
            $id    = \Xmf\Request::getInt('id', 0, 'GET');
            if (empty($id)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $hMsVat->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_MYSERVICES_MODIFY;
        } else {
            $title        = _AM_MYSERVICES_ADD_VAT;
            $item         = $hMsVat->create(true);
            $label_submit = _AM_MYSERVICES_ADD;
            $edit         = false;
        }
        $sform = new \XoopsThemeForm($title, 'frmadd' . $object, $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'saveedit' . $object));
        $sform->addElement(new \XoopsFormHidden('vat_id', $item->getVar('vat_id')));
        $sform->addElement(new \XoopsFormText(_MYSERVICES_RATE, 'vat_rate', 10, 15, $item->getVar('vat_rate', 'e')), true);

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform =\XoopsModules\Myservices\Utilities::formMarkRequiredFields($sform);
        $sform->display();
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'employees':        // Employee List
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(2);
        $tblItems = [];
        $objet    = 'employees';
        $form     = "<form method='post' action='$baseurl' name='frmadd$objet' id='frmadd$objet'><input type='hidden' name='op' id='op' value='add$objet'><input type='submit' name='btngo' id='btngo' value='" . _AM_MYSERVICES_ADD_ITEM . "'></form>";
        echo $form;
       \XoopsModules\Myservices\Utilities::htitle(_MI_MYSERVICES_ADMENU2, 4);

        $start = \Xmf\Request::getInt('start', 0, 'GET');

        $itemsCount = 0;
        $tblItems   = [];
        $itemsCount = $hMsEmployees->getCount();    // Recherche du nombre total d'�l�ments
        $pagenav    = new \XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=' . $objet);

        $tblItems = $hMsEmployees->getItems($start, $limit);
        $class    = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><td colspan='2' align='left'>" . $pagenav->renderNav() . "</td><td align='right' colspan='3'>&nbsp;</td></tr>\n";
        echo "<tr><th align='center'>" . _MYSERVICES_LASTNAME . '&nbsp;' . _MYSERVICES_FIRSTNAME . "</th><th align='center'>" . _MYSERVICES_EMAIL . "</th><th align='center'>" . _MYSERVICES_ISACTIVE . "</th><th align='center'>" . _AM_MYSERVICES_ACTION . '</th></tr>';
        foreach ($tblItems as $item) {
            $id            = $item->getVar('employees_id');
            $class         = ('even' === $class) ? 'odd' : 'even';
            $isActive      = 1 == $item->getVar('employees_isactive') ? _YES : _NO;
            $action_edit   = "<a href='$baseurl?op=edit" . $objet . '&id=' . $id . "' title='" . _MYSERVICES_EDIT . "'>" . $icones['edit'] . '</a>';
            $action_delete = "<a href='$baseurl?op=delete" . $objet . '&id=' . $id . "' title='" . _MYSERVICES_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            echo "<td><a href='"
                 . $item->getEmployeeLink()
                 . "' target='_blank'>"
                 . $item->getEmployeeFullName()
                 . "</a></td><td align='center'>"
                 . $item->getVar('employees_email')
                 . "</td><td align='center'>"
                 . $isActive
                 . "</td><td align='center'>"
                 . $action_edit
                 . ' '
                 . $action_delete
                 . "</td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='4' align='center'>" . $form . "</td>\n";
        echo "</tr>\n";
        echo '</table>';
        echo "<div align='right'>" . $pagenav->renderNav() . '</div>';
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'saveeditemployees':    // Save an employee
        // ****************************************************************************************************************
        xoops_cp_header();
        $id         = \Xmf\Request::getInt('employees_id', 0, 'POST');
        $opRedirect = 'employees';
        if (!empty($id)) {
            $edit = true;
            $item = $hMsEmployees->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $hMsEmployees->create(true);
        }

        $item->setVars($_POST);

        // Possible Deleting images
        for ($i = 1; $i <= 5; ++$i) {
            $name      = sprintf('employees_photo%d', $i);
            $fieldName = 'delpicture' . $i;
            if (isset($_POST[$fieldName]) && 1 == \Xmf\Request::getInt($fieldName, 0, 'POST')) {
                $item->setVar($name, '');
            }
        }

        // Upload file
        for ($i = 0; $i <= 4; ++$i) {
            if (myservices_upload($i)) {
                $name = sprintf('employees_photo%d', $i + 1);
                $item->setVar($name, basename($destname));
            }
        }
        $res = $hMsEmployees->insert($item);
        if ($res) {
           \XoopsModules\Myservices\Utilities::updateCache();
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'deleteemployees':    // Delete an employee
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'employees';

        //  Check that the services of that employee are not used in a command
        $criteria = new \Criteria('caddy_employees_id', $id, '=');
        $cnt      = $hMsCaddy->getCount($criteria);
        if (0 == $cnt) {
            $item = null;
            $item = $hMsEmployees->get($id);
            if (is_object($item)) {
                $res = $hMsEmployees->delete($item, true);
                if ($res) {
                   \XoopsModules\Myservices\Utilities::updateCache();
                   \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
                } else {
                   \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
                }
            } else {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_5, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'addemployees': // Add an employee
    case 'editemployees': // Edition Employee
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(2);
        $objet = 'employees';
        if ($op == 'edit' . $objet) {
            $title = _AM_MYSERVICES_EDIT_EMPL;
            $id    = \Xmf\Request::getInt('id', 0, 'GET');
            if (empty($id)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $hMsEmployees->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_MYSERVICES_MODIFY;
        } else {
            $title        = _AM_MYSERVICES_ADD_EMPL;
            $item         = $hMsEmployees->create(true);
            $label_submit = _AM_MYSERVICES_ADD;
            $edit         = false;
        }

        $sform = new \XoopsThemeForm($title, 'frmm' . $objet, $baseurl);
        $sform->setExtra('enctype="multipart/form-data"');
        $sform->addElement(new \XoopsFormHidden('op', 'saveedit' . $objet));
        $sform->addElement(new \XoopsFormHidden('employees_id', $item->getVar('employees_id')));
        $sform->addElement(new \XoopsFormText(_MYSERVICES_LASTNAME, 'employees_lastname', 50, 50, $item->getVar('employees_lastname', 'e')), true);
        $sform->addElement(new \XoopsFormText(_MYSERVICES_FIRSTNAME, 'employees_firstname', 50, 50, $item->getVar('employees_firstname', 'e')), true);
        $sform->addElement(new \XoopsFormText(_MYSERVICES_EMAIL, 'employees_email', 50, 150, $item->getVar('employees_email', 'e')), false);
        $sform->addElement(new \XoopsFormRadioYN(_MYSERVICES_ISACTIVE, 'employees_isactive', $item->getVar('employees_isactive')), true);
        $editor =\XoopsModules\Myservices\Utilities::getWysiwygForm(_MYSERVICES_BIO, 'employees_bio', $item->getVar('employees_bio', 'e'), 15, 60, 'bio_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }

        for ($i = 1; $i <= 5; ++$i) {
            if ($op == 'edit' . $objet && '' != trim($item->getVar('employees_photo' . $i)) && file_exists(XOOPS_UPLOAD_PATH . '/' . trim($item->getVar('employees_photo' . $i)))) {
                $pictureTray = new \XoopsFormElementTray(_AM_MYSERVICES_CURRENT_PICTURE, '<br>');
                $pictureTray->addElement(new \XoopsFormLabel('', "<img src='" . XOOPS_UPLOAD_URL . '/' . $item->getVar('employees_photo' . $i) . "' alt='' border='0'>"));
                $deleteCheckbox = new \XoopsFormCheckBox('', 'delpicture' . $i);
                $deleteCheckbox->addOption(1, _DELETE);
                $pictureTray->addElement($deleteCheckbox);
                $sform->addElement($pictureTray);
                unset($pictureTray, $deleteCheckbox);
            }
            $sform->addElement(new \XoopsFormFile(_AM_MYSERVICES_PICTURE, 'attachedfile' . $i,\XoopsModules\Myservices\Utilities::getModuleOption('maxuploadsize')), false);
        }

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);

        $sform =\XoopsModules\Myservices\Utilities::formMarkRequiredFields($sform);
        $sform->display();
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'instant-zero':    // Publicity
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(9);
        echo "<iframe src='http://www.instant-zero.com/modules/liaise/?form_id=2' width='100%' height='600' frameborder='0'></iframe>";
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'categories':    // Categories list
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(4);
        $objet = 'categories';
        // Display categories **********************************************************************
        $tblItems = [];
       \XoopsModules\Myservices\Utilities::htitle(_MI_MYSERVICES_ADMENU4, 4);

        $tblItems    = $hMsCategories->getItems();
        $mytree      = new \XoopsObjectTree($tblItems, 'categories_id', 'categories_pid');
        $selectCateg = $mytree->makeSelBox('id', 'categories_title');

        echo "<div class='even'><form method='post' name='quickaccess' id='quickaccess' action='$baseurl' >"
             . _AM_MYSERVICES_LIST
             . " $selectCateg<input type='radio' name='op' id='op' value='editcategories'>"
             . _EDIT
             . " <input type='radio' name='op' id='op' value='deletecategories'>"
             . _DELETE
             . " <input type='submit' name='btnquick' id='btnquick' value='"
             . _GO
             . "'></form></div>";
        echo "<div class='odd' align='center'><form method='post' name='frmadd' id='frmadd' action='$baseurl' ><input type='hidden' name='op' id='op' value='addcategories'><input type='submit' name='btnadd' id='btnadd' value='" . _AM_MYSERVICES_ADD_CATEG . "'></form></div>";
        echo '<br><br>';

        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'addcategories': // Add a Category
    case 'editcategories': // Editing a category
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(4);
        $objet = 'categories';
        if ($op == 'edit' . $objet) {
            $title = _AM_MYSERVICES_EDIT_CATEG;
            $id    = \Xmf\Request::getInt('id', 0, 'POST');
            if (empty($id)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $hMsCategories->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_MYSERVICES_MODIFY;
        } else {
            $title        = _AM_MYSERVICES_ADD_CATEG;
            $item         = $hMsCategories->create(true);
            $label_submit = _AM_MYSERVICES_ADD;
            $edit         = false;
        }
        $tblCategories = [];
        $tblCategories = $hMsCategories->getItems();
        $mytree        = new \XoopsObjectTree($tblCategories, 'categories_id', 'categories_pid');
        $selectCateg   = $mytree->makeSelBox('categories_pid', 'categories_title', '-', $item->getVar('categories_pid'), true);

        $sform = new \XoopsThemeForm($title, 'frm' . $objet, $baseurl);
        $sform->setExtra('enctype="multipart/form-data"');
        $sform->addElement(new \XoopsFormHidden('op', 'saveedit' . $objet));
        $sform->addElement(new \XoopsFormHidden('categories_id', $item->getVar('categories_id')));
        $sform->addElement(new \XoopsFormText(_AM_MYSERVICES_CATEG_TITLE, 'categories_title', 50, 255, $item->getVar('categories_title', 'e')), true);
        $sform->addElement(new \XoopsFormLabel(_AM_MYSERVICES_PARENT_CATEG, $selectCateg), true);

        if ($op == 'edit' . $objet && '' != trim($item->getVar('categories_imgurl')) && file_exists(XOOPS_UPLOAD_PATH . '/' . trim($item->getVar('categories_imgurl')))) {
            $pictureTray = new \XoopsFormElementTray(_AM_MYSERVICES_CURRENT_PICTURE, '<br>');
            $pictureTray->addElement(new \XoopsFormLabel('', "<img src='" . XOOPS_UPLOAD_URL . '/' . $item->getVar('categories_imgurl') . "' alt='' border='0'>"));
            $deleteCheckbox = new \XoopsFormCheckBox('', 'delpicture');
            $deleteCheckbox->addOption(1, _DELETE);
            $pictureTray->addElement($deleteCheckbox);
            $sform->addElement($pictureTray);
            unset($pictureTray, $deleteCheckbox);
        }
        $sform->addElement(new \XoopsFormFile(_AM_MYSERVICES_PICTURE, 'attachedfile',\XoopsModules\Myservices\Utilities::getModuleOption('maxuploadsize')), false);
        $editor =\XoopsModules\Myservices\Utilities::getWysiwygForm(_AM_MYSERVICES_DESCRIPTION, 'categories_description', $item->getVar('categories_description', 'e'), 15, 60, 'description_hidden');
        if ($editor) {
            $sform->addElement($editor, false);
        }

        $editor2 =\XoopsModules\Myservices\Utilities::getWysiwygForm(_MI_MYSERVICES_ADVERTISEMENT, 'categories_advertisement', $item->getVar('categories_advertisement', 'e'), 15, 60, 'advertisement_hidden');
        if ($editor2) {
            $sform->addElement($editor2, false);
        }

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);

        $sform =\XoopsModules\Myservices\Utilities::formMarkRequiredFields($sform);
        $sform->display();
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'saveeditcategories':    // Save a category
        // ****************************************************************************************************************
        xoops_cp_header();
        $id         = \Xmf\Request::getInt('categories_id', 0, 'POST');
        $opRedirect = 'categories';
        if (!empty($id)) {
            $edit = true;
            $item = $hMsCategories->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
            if (\Xmf\Request::getInt('categories_pid', 0, 'POST') == \Xmf\Request::getInt('categories_id', 0, 'POST')) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_6, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
            $item = $hMsCategories->create(true);
        }

        $item->setVars($_POST);

        if (\Xmf\Request::hasVar('delpicture', 'POST') && 1 == \Xmf\Request::getInt('delpicture', 0, 'POST')) {
            $item->setVar('categories_imgurl', '');
        }

        // Upload du fichier
        if (myservices_upload(0)) {
            $item->setVar('categories_imgurl', basename($destname));
        }
        $res = $hMsCategories->insert($item);
        if ($res) {
           \XoopsModules\Myservices\Utilities::updateCache();
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'deletecategories':    //  Delete a category
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(4);
        $id = \Xmf\Request::getInt('id', 0, 'POST');
        if (0 == $id) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
        }
        $category = null;
        $category = $hMsCategories->get($id);
        if (!is_object($category)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_14, $baseurl, 5);
        }
        $msg = sprintf(_AM_MYSERVICES_CONF_DEL_CATEG, $category->getVar('categories_title'));
        xoops_confirm(['op' => 'confdeletecategory', 'id' => $id], 'index.php', $msg);
        break;

    // ****************************************************************************************************************
    case 'confdeletecategory':    // effective Deleting a category (after confirmation of deletion)
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(4);
        $id = \Xmf\Request::getInt('id', 0, 'POST');
        if (0 == $id) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
        }
        $category = null;
        $category = $hMsCategories->get($id);
        if (!is_object($category)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_14, $baseurl, 5);
        }
        $opRedirect = 'categories';
        // Check that this category is not used by products
        $tblCategories = $tblChilds = $tblChidsIds = [];
        $cnt           = 0;
        $lstIds        = '';
        // Search subcategories in this category
        $tblCategories = $hMsCategories->getItems();
        $mytree        = new \XoopsObjectTree($tblCategories, 'categories_id', 'categories_pid');
        $tblChilds     = $mytree->getAllChild($id);
        $tblChidsIds[] = $id;
        if (count($tblChilds) > 0) {
            foreach ($tblChilds as $onechild) {
                $tblChidsIds[] = $onechild->getVar('categories_id');
            }
        }
        $lstIds   = implode(',', $tblChidsIds);
        $criteria = new \Criteria('products_categories_id', '(' . $lstIds . ')', 'IN');
        $cnt      = $hMsProducts->getCount($criteria);
        if (0 == $cnt) {
            $item = null;
            $item = $hMsCategories->get($id);
            if (is_object($item)) {
                $critere = new \Criteria('categories_id', $id, '=');
                $res     = $hMsCategories->deleteAll($critere);
                if ($res) {
                   \XoopsModules\Myservices\Utilities::updateCache();
                   \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
                } else {
                   \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
                }
            } else {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_4, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'products':    // Product Management
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(5);
        $objet       = 'products';
        $tblProducts = $tblCategories = [];

        // Get unique data
        $tblCategories = $hMsCategories->getItems();

        $mytree       = new \XoopsObjectTree($tblCategories, 'categories_id', 'categories_pid');
        $select_categ = $mytree->makeSelBox('id', 'categories_title');

        echo "<form method='post' action='$baseurl' name='frmaddd$objet' id='frmaddd$objet'><input type='hidden' name='op' id='op' value='add$objet'><input type='submit' name='btngo' id='btngo' value='" . _AM_MYSERVICES_ADD_ITEM . "'></form>";
        echo "<br><form method='get' action='$baseurl' name='frmaddedit$objet' id='frmaddedit$objet'>"
             . _MYSERVICES_PRODUCT_ID
             . " <input type='text' name='id' id='id' value='' size='4'> <input type='radio' name='op' id='op' value='edit$objet'>"
             . _MYSERVICES_EDIT
             . " <input type='radio' name='op' id='op' value='delete$objet'>"
             . _MYSERVICES_DELETE
             . " <input type='submit' name='btngo' id='btngo' value='"
             . _GO
             . "'></form>";
       \XoopsModules\Myservices\Utilities::htitle(_MI_MYSERVICES_ADMENU5, 4);

        $start    = \Xmf\Request::getInt('start', 0, 'GET');
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('products_id', 0, '<>'));

        $itemsCount = $hMsProducts->getCount($criteria);    // Recherche du nombre total de produits
        $pagenav    = new \XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=' . $objet);

        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort('products_title');

        $tblVat      = [];
        $tblVat      = $hMsVat->getItems();
        $tblProducts = $hMsProducts->getObjects($criteria);
        $class       = '';
        echo "<div align='left'>" . $pagenav->renderNav() . '</div>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>"
             . _MYSERVICES_PRODUCT_TITLE
             . "</th><th align='center'>"
             . _MYSERVICES_PRODUCT_CATEGORY
             . "</th><th align='center'>"
             . _MYSERVICES_PRODUCT_VAT
             . "</th><th align='center'>"
             . _MYSERVICES_PRODUCT_PRICE_HT
             . "</th><th align='center'>"
             . _MYSERVICES_ONLINE
             . "</th><th align='center'>"
             . _AM_MYSERVICES_ACTION
             . '</th></tr>';
        foreach ($tblProducts as $item) {
            $class            = ('even' === $class) ? 'odd' : 'even';
            $itemId           = $item->getVar('products_id');
            $action_edit      = "<a href='$baseurl?op=edit" . $objet . '&id=' . $itemId . "' title='" . _MYSERVICES_EDIT . "'>" . $icones['edit'] . '</a>';
            $action_duplicate = "<a href='$baseurl?op=copy" . $objet . '&id=' . $itemId . "' title='" . _MYSERVICES_DUPLICATE_PRODUCT . "'>" . $icones['copy'] . '</a>';
            $action_delete    = "<a href='$baseurl?op=delete" . $objet . '&id=' . $itemId . "' title='" . _MYSERVICES_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            $online           = 1 == $item->getVar('products_online') ? _YES : _NO;
            echo "<tr class='" . $class . "'>\n";
            $productCategory = isset($tblCategories[$item->getVar('products_categories_id')]) ? $tblCategories[$item->getVar('products_categories_id')]->getVar('categories_title') : '';
            $productVat      = isset($tblVat[$item->getVar('products_vat_id')]) ? $tblVat[$item->getVar('products_vat_id')]->getVar('vat_rate') : '';
            echo "<td><a href='"
                 . $item->getProductLink()
                 . "' target='_blank'>"
                 . $item->getVar('products_title')
                 . "</a></td><td align='left'>"
                 . $productCategory
                 . "</td><td align='right'>"
                 . $productVat
                 . "</td><td align='right'>"
                 . $myservices_Currency->amountForDisplay($item->getVar('products_price'))
                 . "</td><td align='center'>"
                 . $online
                 . "</td><td align='center'>"
                 . $action_edit
                 . '&nbsp;'
                 . $action_duplicate
                 . '&nbsp;'
                 . $action_delete
                 . "</td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='6' align='center'><form method='post' action='$baseurl' name='frmaddd$objet' id='frmaddd$objet'><input type='hidden' name='op' id='op' value='add$objet'><input type='submit' name='btngo' id='btngo' value='" . _AM_MYSERVICES_ADD_ITEM . "'></form></td>\n";
        echo "</tr>\n";
        echo '</table>';
        echo "<div align='right'>" . $pagenav->renderNav() . '</div>';
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'addproducts':        // Add a product
    case 'editproducts':    // Edit a product
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(5);
        global $xoopsUser;
        $objet = 'products';

        if ($op == 'edit' . $objet) {
            $title = _AM_MYSERVICES_EDIT_PRODUCT;
            $id    = \Xmf\Request::getInt('id', 0, 'GET');
            if (empty($id)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $hMsProducts->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_MYSERVICES_MODIFY;
        } else {
            $title        = _AM_MYSERVICES_ADD_PRODUCT;
            $item         = $hMsProducts->create(true);
            $label_submit = _AM_MYSERVICES_ADD;
            $edit         = false;
        }
        $tblCategories = [];
        $tblCategories = $hMsCategories->getItems();
        if (0 == count($tblCategories)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_8, $baseurl, 5);
        }
        $mytree      = new \XoopsObjectTree($tblCategories, 'categories_id', 'categories_pid');
        $selectCateg = $mytree->makeSelBox('products_categories_id', 'categories_title', '-', $item->getVar('products_categories_id'));

        // VAT
        $tblVat = [];
        $tblVat = $hMsVat->getList();
        if (0 == count($tblVat)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_9, $baseurl, 5);
        }

        // People offering this service ************************************
        $tblEmployees = $tblEmployeesProducts = $tblEmployeesProductsForm = $tblEmployeesForm = [];

        // Search for all employees
        $tblEmployees = $hMsEmployees->getItems();
        if (0 == count($tblEmployees)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_10, $baseurl, 5);
        }

        foreach ($tblEmployees as $oneitem) {
            $tblEmployeesForm[$oneitem->getVar('employees_id')] = xoops_trim($oneitem->getVar('employees_lastname')) . ' ' . xoops_trim($oneitem->getVar('employees_firstname'));
        }
        // Search for employees who provide this service
        if ($edit) {
            $criteria            = new \Criteria('employeesproducts_products_id', $item->getVar('products_id'), '=');
            $tblEmployeesProducts = $hMsEmployeesProducts->getObjects($criteria);
            foreach ($tblEmployeesProducts as $item2) {
                $tblEmployeesProductsForm[] = $item2->getVar('employeesproducts_employees_id');
            }
        }

        $sform = new \XoopsThemeForm($title, 'frm' . $objet, $baseurl);
        $sform->setExtra('enctype="multipart/form-data"');

        $sform->addElement(new \XoopsFormHidden('op', 'saveedit' . $objet));
        $sform->addElement(new \XoopsFormHidden('products_id', $item->getVar('products_id')));
        $sform->addElement(new \XoopsFormText(_MYSERVICES_PRODUCT_TITLE, 'products_title', 50, 255, $item->getVar('products_title', 'e')), true);

        // Category *******************************************************
        $sform->addElement(new \XoopsFormLabel(_MYSERVICES_PRODUCT_CATEGORY, $selectCateg), true);
        $sform->addElement(new \XoopsFormRadioYN(_MYSERVICES_ONLINE, 'products_online', $item->getVar('products_online')), true);
        $sform->addElement(new \XoopsFormText(_MYSERVICES_DURATION, 'products_duration', 5, 10, $item->getVar('products_duration', 'e')), true);

        // VAT *************************************************************
        $vatSelect = new \XoopsFormSelect(_MYSERVICES_PRODUCT_VAT, 'products_vat_id', $item->getVar('products_vat_id'));
        $vatSelect->addOptionArray($tblVat);
        $sform->addElement($vatSelect, true);
        $sform->addElement(new \XoopsFormText(_AM_MYSERVICES_PRODUCT_PRICE_HT, 'products_price', 20, 20, $item->getVar('products_price', 'e')), true);
        $editor =\XoopsModules\Myservices\Utilities::getWysiwygForm(_MYSERVICES_PRODUCT_SUMMARY, 'products_summary', $item->getVar('products_summary', 'e'), 15, 60, 'summary_hidden');
        if ($editor) {
            $sform->addElement($editor, true);
        }

        $editor2 =\XoopsModules\Myservices\Utilities::getWysiwygForm(_MYSERVICES_PRODUCT_DESC, 'products_description', $item->getVar('products_description', 'e'), 15, 60, 'description_hidden');
        if ($editor2) {
            $sform->addElement($editor2, false);
        }

        $sform->addElement(new \XoopsFormText(_MYSERVICES_PRODUCT_QUALITY, 'products_quality_link', 50, 255, $item->getVar('products_quality_link', 'e')), false);

        // Employees
        $employeesSelect = new \XoopsFormSelect(_MYSERVICES_PRODUCT_EMPLOYES, 'employees', $tblEmployeesProductsForm, 5, true);
        $employeesSelect->addOptionArray($tblEmployeesForm);
        $employeesSelect->setDescription(_AM_MYSERVICES_SELECT_HLP);
        $sform->addElement($employeesSelect, true);

        // Images *************************************************************
        $sform->insertBreak('<b><span style="text-align: center; text-decoration: underline;">' . _AM_MYSERVICES_PHOTOSDESC . '</span></b>', 'foot');
        $maxUpload =\XoopsModules\Myservices\Utilities::getModuleOption('maxuploadsize');
        for ($i = 1; $i <= 10; ++$i) {
            if ($op == 'edit' . $objet && '' != trim($item->getVar('products_image' . $i)) && file_exists(XOOPS_UPLOAD_PATH . '/' . trim($item->getVar('products_image' . $i)))) {
                $pictureTray = new \XoopsFormElementTray(_MYSERVICES_CURRENT_PICTURE, '<br>');
                $pictureTray->addElement(new \XoopsFormLabel('', "<img src='" . XOOPS_UPLOAD_URL . '/' . $item->getVar('products_image' . $i) . "' alt='' border='0'>"), false);
                $deleteCheckbox = new \XoopsFormCheckBox('', 'delpicture' . $i);
                $deleteCheckbox->addOption(1, _DELETE);
                $pictureTray->addElement($deleteCheckbox);
                $sform->addElement($pictureTray);
                unset($pictureTray, $deleteCheckbox);
            }
            $sform->addElement(new \XoopsFormFile(_MYSERVICES_CHANGE_PICTURE, 'attachedfile' . $i, $maxUpload), false);
        }

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);

        $sform =\XoopsModules\Myservices\Utilities::formMarkRequiredFields($sform);
        $sform->display();
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'saveeditproducts':    // Save the information of a product
        // ****************************************************************************************************************
        xoops_cp_header();
        $id    = \Xmf\Request::getInt('products_id', 0, 'POST');
        $objet = 'products';
        if ($id > 0) {
            $edit = true;
            $item = $hMsProducts->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
            $add = false;
        } else {
            $item = $hMsProducts->create(true);
            $edit = false;
            $add  = true;
        }

        $item->setVars($_POST);

        // Possible Deleting images
        for ($i = 1; $i <= 10; ++$i) {
            $fieldName = 'delpicture' . $i;
            if (isset($_POST[$fieldName]) && 1 == \Xmf\Request::getInt($fieldName, 0, 'POST')) {
                $name = sprintf('products_image%d', $i);
                $item->setVar($name, '');
            }
        }

        for ($i = 0; $i < 10; ++$i) {
            if (myservices_upload($i)) {
                $name = sprintf('products_image%d', $i + 1);
                $item->setVar($name, basename($destname));
            }
        }

        $res = $hMsProducts->insert($item);
        if ($res) {
            $id = $item->getVar('products_id');
            // People Management  ************************************************
            if ($edit) {
                // Prior Suppression
                $criteria = new \Criteria('employeesproducts_products_id', $id, '=');
                $hMsEmployeesProducts->deleteAll($criteria);
            }
            //  Then data backup
            if (\Xmf\Request::hasVar('employees', 'POST')) {
                foreach ($_POST['employees'] as $id2) {
                    $item2 = $hMsEmployeesProducts->create(true);
                    $item2->setVar('employeesproducts_products_id', $id);
                    $item2->setVar('employeesproducts_employees_id', (int)$id2);
                    $res = $hMsEmployeesProducts->insert($item2);
                }
            }
           \XoopsModules\Myservices\Utilities::updateCache();
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $objet, 2);
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $objet, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'copyproducts':    // Copy a product
        // ****************************************************************************************************************
        xoops_cp_header();
        $id    = \Xmf\Request::getInt('id', 0, 'GET');
        $objet = 'products';
        if (empty($id)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
        }
        $product = null;
        $product = $hMsProducts->get($id);
        if (is_object($product)) {
            $newProduct = $product->xoopsClone();
            $newProduct->setVar('products_title', $product->getVar('products_title') . ' ' . _AM_MYSERVICES_DUPLICATED);
            $newProduct->setVar('products_id', 0);
            $newProduct->setNew();
            $res = $hMsProducts->insert($newProduct, true);
            if ($res) {
                $newProductId = $newProduct->getVar('products_id');
                // Copie des employ�s
                $tblTmp   = [];
                $criteria = new \Criteria('employeesproducts_products_id', $product->getVar('products_id'), '=');
                $tblTmp   = $hMsEmployeesProducts->getObjects($criteria);
                foreach ($tblTmp as $productEmploye) {
                    $newProductEmploye = $productEmploye->xoopsClone();
                    $newProductEmploye->setVar('employeesproducts_products_id', $newProductId);
                    $newProductEmploye->setVar('employeesproducts_id', 0);
                    $newProductEmploye->setNew();
                    $hMsEmployeesProducts->insert($newProductEmploye, true);
                }
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $objet, 2);
            } else {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $objet, 5);
            }
        }
        break;

    // ****************************************************************************************************************
    case 'deleteproducts':    // Delete a product
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (0 == $id) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
        }
        $objet  = 'products';
        $tblTmp = [];
        $tblTmp = $hMsCaddy->getCommandIdFromProduct($id);
        if (0 == count($tblTmp)) {    // Il n'y a pas de commande rattach�e � ce produit
            // Suppression des personnes qui assurent ce service
            $criteria = new \Criteria('employeesproducts_products_id', $id, '=');
            $hMsEmployeesProducts->deleteAll($criteria);
            // Puis le produit
            $item = null;
            $item = $hMsProducts->get($id);
            if (is_object($item)) {
                $res = $hMsProducts->delete($item, true);
                if ($res) {
                   \XoopsModules\Myservices\Utilities::updateCache();
                   \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $objet, 2);
                } else {
                   \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $objet, 5);
                }
            } else {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl . '?op=' . $objet, 5);
            }
        } else {
            // myservices_adminMenu(5);
           \XoopsModules\Myservices\Utilities::htitle(_AM_MYSERVICES_SORRY_NOREMOVE, 4);
            $tblTmp2 = [];
            $tblTmp2 = $hMsOrders->getObjects(new \Criteria('orders_id', '(' . implode(',', $tblTmp) . ')', 'IN'), true);
            echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
            $class = '';
            echo "<tr><th align='center'>" . _AM_MYSERVICES_ID . "</th><th align='center'>" . _AM_MYSERVICES_DATE . "</th><th align='center'>" . _AM_MYSERVICES_CLIENT . '</th></tr>';
            foreach ($tblTmp2 as $item) {
                $class = ('even' === $class) ? 'odd' : 'even';
                $date  =\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($item->getVar('orders_date'));
                echo "<tr class='" . $class . "'>\n";
                echo "<td align='right'>"
                     . $item->getVar('orders_id')
                     . "</td><td align='center'>"
                     . $date
                     . "</td><td align='center'>"
                     . $item->getVar('orders_lastname')
                     . ' '
                     . $item->getVar('orders_firstname')
                     . "</td><td align='center'>"
                     . $myservices_Currency->amountForDisplay($item->getVar('orders_total'))
                     . "</td>\n";
                echo "<tr>\n";
            }
            echo '</table>';
            require_once __DIR__ . '/admin_footer.php';  //show_footer();
        }
        break;

    // ****************************************************************************************************************
    case 'holiday':    // Management holidays and days worked (but the order has been placed off-site)
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(3);
        $objet = 'holiday';

       \XoopsModules\Myservices\Utilities::htitle(_MI_MYSERVICES_ADMENU3, 4);
       \XoopsModules\Myservices\Utilities::htitle(_MYSERVICES_EMPLOYES_IN_HOLIDAYS, 5);
        echo " <a href='$baseurl?op=csv$objet'>" . _AM_MYSERVICES_CSV_EXPORT . '</a>';

        // Search for people on vacation
        $start = \Xmf\Request::getInt('start', 0, 'GET');

        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('calendar_status', CALENDAR_STATUS_HOLIDAY, '='));
        $critere->add(new \Criteria('calendar_status', CALENDAR_STATUS_WORK, '='), 'OR');

        $itemsCount = 0;
        $tblItems   = $employeesList = $tblEmployees = [];
        $itemsCount = $hMsCalendar->getCount($critere);    // Total Search
        $pagenav    = new \XoopsPageNav($itemsCount, $limit, $start, 'start', 'op=' . $objet);

        $critere->setSort('calendar_start');
        $critere->setOrder('DESC');
        $critere->setLimit($limit);
        $critere->setStart($start);
        $tblItems = $hMsCalendar->getObjects($critere);

        foreach ($tblItems as $item) {
            $employeesList[] = $item->getVar('calendar_employees_id');
        }
        if (count($employeesList) > 0) {
            $critere     = new \Criteria('employees_id', '(' . implode(',', $employeesList) . ')', 'IN');
            $tblEmployees = $hMsEmployees->getObjects($critere, true);
        }

        $class = '';
        echo "<div align='left'>" . $pagenav->renderNav() . '</div>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _MYSERVICES_EMPLOYE . "</th><th align='center'>" . _MYSERVICES_STARTING_DATE . "</th><th align='center'>" . _MYSERVICES_ENDING_DATE . "</th><th align='center'>" . _AM_MYSERVICES_STATE . "</th><th align='center'>" . _AM_MYSERVICES_ACTION . '</th></tr>';
        foreach ($tblItems as $item) {
            $class         = ('even' === $class) ? 'odd' : 'even';
            $itemId        = $item->getVar('calendar_id');
            $action_edit   = "<a href='$baseurl?op=edit" . $objet . '&status=' . $item->getVar('calendar_status') . '&id=' . $itemId . "' title='" . _MYSERVICES_EDIT . "'>" . $icones['edit'] . '</a>';
            $action_delete = "<a href='$baseurl?op=delete" . $objet . '&id=' . $itemId . "' title='" . _MYSERVICES_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            $employee = isset($tblEmployees[$item->getVar('calendar_employees_id')]) ? $tblEmployees[$item->getVar('calendar_employees_id')]->getVar('employees_lastname') . ' ' . $tblEmployees[$item->getVar('calendar_employees_id')]->getVar('employees_firstname') : _MYSERVICES_UNKNOW_EMPLOYE;
            echo '<td>'
                 . $employee
                 . "</td><td align='center'>"
                 .\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($item->getVar('calendar_start'))
                 . "</td><td align='center'>"
                 .\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($item->getVar('calendar_end'))
                 . "</td><td align='center'>"
                 . $item->getStatusLabel()
                 . "</td><td align='center'>"
                 . $action_edit
                 . '&nbsp;'
                 . $action_delete
                 . "</td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='6' align='center'><form method='post' action='$baseurl' name='frmaddd$objet' id='frmaddd$objet'><input type='hidden' name='status' id='status' value='"
             . CALENDAR_STATUS_HOLIDAY
             . "'><input type='hidden' name='op' id='op' value='add$objet'><input type='submit' name='btngo' id='btngo' value='"
             . _AM_MYSERVICES_ADD_ITEM
             . "'></form></td>\n";
        echo "</tr>\n";
        echo '</table>';
        echo "<div align='right'>" . $pagenav->renderNav() . '</div>';

        // Store Closed ******************************************************************************
       \XoopsModules\Myservices\Utilities::htitle(_AM_MYSERVICES_CLOSE_SHOP, 5);
        $itemsCount = 0;
        $tblItems   = [];
        $start2     = \Xmf\Request::getInt('start2', 0, 'GET');
        $critere    = new \Criteria('calendar_status', CALENDAR_STATUS_CLOSED, '=');
        $itemsCount = $hMsCalendar->getCount($critere);    // Recherche du total
        if ($itemsCount > $limit) {
            $pagenav = new \XoopsPageNav($itemsCount, $limit, $start2, 'start2', 'op=' . $objet);
        }
        $critere->setSort('calendar_start');
        $critere->setOrder('DESC');
        $critere->setLimit($limit);
        $critere->setStart($start);
        $tblItems = $hMsCalendar->getObjects($critere);
        $class    = '';
        echo "<div align='left'>" . $pagenav->renderNav() . '</div>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _MYSERVICES_STARTING_DATE . "</th><th align='center'>" . _MYSERVICES_ENDING_DATE . "</th><th align='center'>" . _AM_MYSERVICES_ACTION . '</th></tr>';
        foreach ($tblItems as $item) {
            $class         = ('even' === $class) ? 'odd' : 'even';
            $itemId        = $item->getVar('calendar_id');
            $action_edit   = "<a href='$baseurl?op=edit" . $objet . '&status=' . $item->getVar('calendar_status') . '&id=' . $itemId . "' title='" . _MYSERVICES_EDIT . "'>" . $icones['edit'] . '</a>';
            $action_delete = "<a href='$baseurl?op=delete" . $objet . '&id=' . $itemId . "' title='" . _MYSERVICES_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='center'>" .\XoopsModules\Myservices\Utilities::SQLDateToHuman($item->getVar('calendar_start'), 's') . "</td><td align='center'>" .\XoopsModules\Myservices\Utilities::SQLDateToHuman($item->getVar('calendar_end'), 's') . "</td><td align='center'>" . $action_edit . '&nbsp;' . $action_delete . "</td>\n";
            echo "<tr>\n";
        }
        $class = ('even' === $class) ? 'odd' : 'even';
        echo "<tr class='" . $class . "'>\n";
        echo "<td colspan='5' align='center'><form method='post' action='$baseurl' name='frmaddd$objet' id='frmaddd$objet'><input type='hidden' name='status' id='status' value='"
             . CALENDAR_STATUS_CLOSED
             . "'><input type='hidden' name='op' id='op' value='add$objet'><input type='submit' name='btngo' id='btngo' value='"
             . _AM_MYSERVICES_ADD_ITEM
             . "'></form></td>\n";
        echo "</tr>\n";
        echo '</table>';
        echo "<div align='right'>" . $pagenav->renderNav() . '</div>';
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'addholiday': // Add a person on leave
    case 'editholiday': // Editing leave a person
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(3);
        $object = 'holiday';

        if (\Xmf\Request::hasVar('status', 'POST')) {
 $status = \Xmf\Request::getInt('status', 0, 'POST');
} elseif (\Xmf\Request::hasVar('status', 'GET')) {
 $status = \Xmf\Request::getInt('status', 0, 'GET');
} else {
            $status = CALENDAR_STATUS_HOLIDAY;
        }

        if ($op == 'edit' . $object) {
            if (CALENDAR_STATUS_HOLIDAY == $status || CALENDAR_STATUS_WORK == $status) {
                $title = _AM_MYSERVICES_EDIT_HOLIDAY;
            } else {
                $title = _AM_MYSERVICES_EDIT_CLOSE;
            }
            $id = \Xmf\Request::getInt('id', 0, 'GET');
            if (empty($id)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
            }
            // Item exits ?
            $item = null;
            $item = $hMsCalendar->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $edit         = true;
            $label_submit = _AM_MYSERVICES_MODIFY;
        } else {
            if (CALENDAR_STATUS_HOLIDAY == $status || CALENDAR_STATUS_WORK == $status) {
                $title = _AM_MYSERVICES_ADD_HOLIDAY;
            } else {
                $title = _AM_MYSERVICES_ADD_CLOSE;
            }
            $item         = $hMsCalendar->create(true);
            $label_submit = _AM_MYSERVICES_ADD;
            $item->setVar('calendar_status', $status);
            $edit = false;
        }

        $sform = new \XoopsThemeForm($title, 'frmadd' . $object, $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'saveedit' . $object));
        $sform->addElement(new \XoopsFormHidden('calendar_id', $item->getVar('calendar_id')));

        if (CALENDAR_STATUS_HOLIDAY == $status || CALENDAR_STATUS_WORK == $status) {
            // Find the list of employees
            $tblEmployees = $hMsEmployees->getItems();
            if (0 == count($tblEmployees)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_11, $baseurl, 5);
            }
            foreach ($tblEmployees as $oneitem) {
                $tblEmployeesForm[$oneitem->getVar('employees_id')] = xoops_trim($oneitem->getVar('employees_lastname')) . ' ' . xoops_trim($oneitem->getVar('employees_firstname'));
            }
            $employeesSelect = new \XoopsFormSelect(_MYSERVICES_EMPLOYE, 'calendar_employees_id', $item->getVar('calendar_employees_id'), 1, false);
            $employeesSelect->addOptionArray($tblEmployeesForm);
            $sform->addElement($employeesSelect, true);
        } else {
            $sform->addElement(new \XoopsFormHidden('employees_id', -1));
        }

        if (CALENDAR_STATUS_CLOSED != $status) {
            $sform->addElement(new \XoopsFormDateTime(_MYSERVICES_STARTING_DATE, 'calendar_start', 15, strtotime($item->getVar('calendar_start', 'e'))), true);
            $sform->addElement(new \XoopsFormDateTime(_MYSERVICES_ENDING_DATE, 'calendar_end', 15, strtotime($item->getVar('calendar_end', 'e'))), true);
        } else {
            $sform->addElement(new \XoopsFormTextDateSelect(_MYSERVICES_STARTING_DATE, 'calendar_start', 15, strtotime($item->getVar('calendar_start', 'e'))), true);
            $sform->addElement(new \XoopsFormTextDateSelect(_MYSERVICES_ENDING_DATE, 'calendar_end', 15, strtotime($item->getVar('calendar_end', 'e'))), true);
        }

        if (CALENDAR_STATUS_HOLIDAY == $status || CALENDAR_STATUS_WORK == $status) {
            $statusRadio = new \XoopsFormRadio(_AM_MYSERVICES_STATE, 'calendar_status', $item->getVar('calendar_status', 'e'));
            $statusRadio->addOptionArray([CALENDAR_STATUS_WORK => _MYSERVICES_STATE_WORK, CALENDAR_STATUS_HOLIDAY => _MYSERVICES_STATE_HOLIDAY]);
            $sform->addElement($statusRadio, true);
        } else {
            $sform->addElement(new \XoopsFormHidden('calendar_status', CALENDAR_STATUS_CLOSED));
        }

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', $label_submit, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform =\XoopsModules\Myservices\Utilities::formMarkRequiredFields($sform);
        $sform->display();
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'saveeditholiday':    // Save time off / closure
        // ****************************************************************************************************************
        xoops_cp_header();
        $id         = \Xmf\Request::getInt('calendar_id', 0, 'POST');
        $opRedirect = 'holiday';
        if (!empty($id)) {
            $edit = true;
            $item = $hMsCalendar->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $hMsCalendar->create(true);
        }

        $item->setVars($_POST);
        if (CALENDAR_STATUS_CLOSED == \Xmf\Request::getInt('calendar_status', 0, 'POST')) {
            $date = strtotime($_POST['calendar_start']);
            $item->setVar('calendar_start',\XoopsModules\Myservices\Utilities::timestampToMysqlDateTime($date));

            $date = strtotime($_POST['calendar_end']);
            $item->setVar('calendar_end',\XoopsModules\Myservices\Utilities::timestampToMysqlDateTime($date));
        } else {
            // Passage des dates au bon format
            $dateForm = $_POST['calendar_start'];
            $date     = strtotime($dateForm['date']) + $dateForm['time'];
            $item->setVar('calendar_start',\XoopsModules\Myservices\Utilities::timestampToMysqlDateTime($date));

            $dateForm = $_POST['calendar_end'];
            $date     = strtotime($dateForm['date']) + $dateForm['time'];
            $item->setVar('calendar_end',\XoopsModules\Myservices\Utilities::timestampToMysqlDateTime($date));
        }

        $res = $hMsCalendar->insert($item);
        if ($res) {
           \XoopsModules\Myservices\Utilities::updateCache();
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'deleteholiday':    // Save time off / closure
        // ****************************************************************************************************************
        xoops_cp_header();
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
        }
        $opRedirect = 'holiday';
        $item       = null;
        $item       = $hMsCalendar->get($id);
        if (is_object($item)) {
            $res = $hMsCalendar->delete($item, true);
            if ($res) {
               \XoopsModules\Myservices\Utilities::updateCache();
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
            } else {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
            }
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'csvholiday':    // Export CSV leave
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(3);
        $filename = 'holidays.csv';
        $fp       = fopen(XOOPS_UPLOAD_PATH . '/' . $filename, 'w');
        if ($fp) {
            // Create the file header
            $entete1 = [];
            $cmd     = new Calendar();
            foreach ($cmd->getVars() as $fieldName => $properties) {
                $entete1[] = $fieldName;
            }
            // Add the employee name (s)
            $entete1[] = 'employee';
            fwrite($fp, implode($s, $entete1) . "\n");
            array_pop($entete1);    // Delete the employee column that was added manually

            // Load the list of employees
            $tblEmployees = $tblEmployeesForm = $tblItems = [];
            $tblEmployees = $hMsEmployees->getItems();
            if (0 == count($tblEmployees)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_10, $baseurl, 5);
            }

            foreach ($tblEmployees as $oneitem) {
                $tblEmployeesForm[$oneitem->getVar('employees_id')] = xoops_trim($oneitem->getVar('employees_lastname')) . ' ' . xoops_trim($oneitem->getVar('employees_firstname'));
            }

            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('calendar_status', CALENDAR_STATUS_HOLIDAY, '='));
            $criteria->setSort('calendar_start');
            $criteria->setOrder('DESC');
            $tblItems = $hMsCalendar->getObjects($criteria);
            foreach ($tblItems as $item) {
                $ligne = [];
                foreach ($entete1 as $commandField) {
                    $ligne[] = $item->getVar($commandField);
                }
                if (isset($tblEmployeesForm[$item->getVar('calendar_employees_id')])) {
                    $ligne[] = $tblEmployeesForm[$item->getVar('calendar_employees_id')];
                }
                fwrite($fp, implode($s, $ligne) . "\n");
            }
            fclose($fp);
            echo "<br><a target='_blank' href='" . XOOPS_UPLOAD_URL . '/' . $filename . "'>" . _AM_MYSERVICES_CSV_READY . '</a>';
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_7);
        }
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'orders':    // Order Management
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(6);
       \XoopsModules\Myservices\Utilities::htitle(_MI_MYSERVICES_ADMENU7, 4);
        $objet = 'orders';

        $start   = \Xmf\Request::getInt('start', 0, 'GET');
        $filter3 = 0;
        if (\Xmf\Request::hasVar('filter3', 'POST')) {
 $filter3 = \Xmf\Request::getInt('filter3', 0, 'POST');
} elseif (\Xmf\Request::hasVar('filter3', 'SESSION')) {
 $filter3 = \Xmf\Request::getInt('filter3', 0, 'SESSION');
} else {
            $filter3 = 1;
        }
        $_SESSION['filter3'] = $filter3;
        $selected            = array_fill(0, 6, '');
        $tblConditions       = [MYSERVICES_ORDER_NOINFORMATION, MYSERVICES_ORDER_VALIDATED, MYSERVICES_ORDER_PENDING, MYSERVICES_ORDER_FAILED, MYSERVICES_ORDER_CANCELED, MYSERVICES_ORDER_FRAUD];
        $selected[$filter3]  = " selected='selected'";

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('orders_id', 0, '<>'));
        $criteria->add(new \Criteria('orders_state', $tblConditions[$filter3], '='));
        $commandsCount = $hMsOrders->getCount($criteria);    // Recherche du nombre total de commandes
        $pagenav       = new \XoopsPageNav($commandsCount, $limit, $start, 'start', 'op=' . $objet);
        $criteria->setSort('orders_date');
        $criteria->setOrder('DESC');
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $tblCommands = $hMsOrders->getObjects($criteria);
        $class       = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        $form                = "<form method='post' name='frmfilter' id='frmfilter' action='$baseurl'>"
                               . _AM_MYSERVICES_LIMIT_TO
                               . " <select name='filter3' id='filter3'><option value='0'"
                               . $selected[0]
                               . '>'
                               . _MYSERVICES_CMD_STATE1
                               . "</option><option value='1'"
                               . $selected[1]
                               . '>'
                               . _MYSERVICES_CMD_STATE2
                               . "</option><option value='2'"
                               . $selected[2]
                               . '>'
                               . _MYSERVICES_CMD_STATE3
                               . "</option><option value='3'"
                               . $selected[3]
                               . '>'
                               . _MYSERVICES_CMD_STATE4
                               . "</option><option value='4'"
                               . $selected[4]
                               . '>'
                               . _MYSERVICES_CMD_STATE5
                               . "</option><option value='5'"
                               . $selected[5]
                               . '>'
                               . _MYSERVICES_CMD_STATE6
                               . "</option></select> <input type='hidden' name='op' id='op' value='orders'><input type='submit' name='btnfilter' id='btnfilter' value='"
                               . _AM_MYSERVICES_FILTER
                               . "'></form>";
        $confValidateCommand =\XoopsModules\Myservices\Utilities::javascriptLinkConfirm(_AM_MYSERVICES_CONF_VALIDATE);
        echo "<tr><td colspan='2' align='left'>" . $pagenav->renderNav() . "</td><td><a href='$baseurl?op=csv&cmdtype=" . $filter3 . "'>" . _AM_MYSERVICES_CSV_EXPORT . "</a></td><td align='right' colspan='2'>" . $form . "</td></tr>\n";
        echo "<tr><th align='center'>" . _AM_MYSERVICES_ID . "</th><th align='center'>" . _AM_MYSERVICES_DATE . "</th><th align='center'>" . _AM_MYSERVICES_CLIENT . "</th><th align='center'>" . _MYSERVICES_TOTAL_TTC . "</th><th align='center'>" . _AM_MYSERVICES_ACTION . '</th></tr>';
        foreach ($tblCommands as $item) {
            $id              = $item->getVar('orders_id');
            $class           = ('even' === $class) ? 'odd' : 'even';
            $date            =\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($item->getVar('orders_date'));
            $action_edit     = "<a target='_blank' href='detailscmd.php?id=" . $id . "' title='" . _MYSERVICES_DETAILS . "'>" . $icones['details'] . '</a>';
            $action_delete   = "<a href='$baseurl?op=delete" . $objet . '&id=' . $id . "' title='" . _MYSERVICES_DELETE . "'" . $conf_msg . '>' . $icones['delete'] . '</a>';
            $action_validate = "<a href='$baseurl?op=validate" . $objet . '&id=' . $id . "' " . $confValidateCommand . " title='" . _MYSERVICES_VALIDATE_COMMAND . "'>" . $icones['ok'] . '</a>';
            echo "<tr class='" . $class . "'>\n";
            echo "<td align='right'>"
                 . $id
                 . "</td><td align='center'>"
                 . $date
                 . "</td><td align='center'>"
                 . $item->getVar('orders_lastname')
                 . ' '
                 . $item->getVar('orders_firstname')
                 . "</td><td align='center'>"
                 . $myservices_Currency->amountForDisplay($item->getVar('orders_total'))
                 . "</td><td align='center'>"
                 . $action_validate
                 . ' '
                 . $action_edit
                 . ' '
                 . $action_delete
                 . "</td>\n";
            echo "<tr>\n";
        }
        echo '</table>';
        echo "<div align='right'>" . $pagenav->renderNav() . '</div>';
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'csv':    // Export orders to CSV
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(6);
       \XoopsModules\Myservices\Utilities::htitle(_MI_MYSERVICES_ADMENU7, 4);
        $cmd_type = \Xmf\Request::getInt('cmdtype', 0, 'GET');
        $filename = 'myservices.csv';
        $fp       = fopen(XOOPS_UPLOAD_PATH . '/' . $filename, 'w');
        if ($fp) {
            // Create the file header
            $entete1 = $entete2 = [];
            $cmd     = new \XoopsModules\Myservices\Orders();
            foreach ($cmd->getVars() as $fieldName => $properties) {
                $entete1[] = $fieldName;
            }
            // Add the information caddy
            $cart = new Myservices\Caddy();
            foreach ($cart->getVars() as $fieldName => $properties) {
                $entete2[] = $fieldName;
            }
            fwrite($fp, implode($s, array_merge($entete1, $entete2)) . "\n");

            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('orders_id', 0, '<>'));
            $criteria->add(new \Criteria('orders_state', $cmd_type, '='));
            $criteria->setSort('orders_date');
            $criteria->setOrder('DESC');
            $tblCommands = $hMsOrders->getObjects($criteria);
            foreach ($tblCommands as $commande) {
                $tblTmp = [];
                $tblTmp = $hMsCaddy->getObjects(new \Criteria('caddy_orders_id', $commande->getVar('orders_id'), '='));
                $ligne  = [];
                foreach ($tblTmp as $caddy) {
                    foreach ($entete1 as $commandField) {
                        $ligne[] = $commande->getVar($commandField);
                    }
                    foreach ($entete2 as $caddyField) {
                        $ligne[] = $caddy->getVar($caddyField);
                    }
                }
                fwrite($fp, implode($s, $ligne) . "\n");
            }
            fclose($fp);
            echo "<a target='_blank' href='" . XOOPS_UPLOAD_URL . '/' . $filename . "'>" . _AM_MYSERVICES_CSV_READY . '</a>';
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_7);
        }
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

    // ****************************************************************************************************************
    case 'deleteorders':    // Delete command
        // ****************************************************************************************************************
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
        }
        $objet = 'orders';
        $item  = $hMsOrders->get($id);
        if (is_object($item)) {
            $res = $hMsOrders->delete($item, true);
            if ($res) {
                // Delete the associated caddy
                $criteria = new \Criteria('caddy_orders_id', $id, '=');
                $tblCaddy = [];
                $tblCaddy = $hMsCaddy->getObjects($criteria);
                foreach ($tblCaddy as $oneCaddy) {
                    //  Delete the agenda if there is one
                    $critereAgenda = new \Criteria('calendar_id', $oneCaddy->getVar('caddy_calendar_id'), '=');
                    $hMsCalendar->deleteAll($critereAgenda);
                    // Then suppression caddy
                    $hMsCaddy->delete($oneCaddy, true);
                }
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $objet, 2);
            } else {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $objet, 5);
            }
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl . '?op=' . $objet, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'validateorders':    // Validation of an order
        // ****************************************************************************************************************
        $id = \Xmf\Request::getInt('id', 0, 'GET');
        if (empty($id)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_1, $baseurl, 5);
        }
        $commande = null;
        $commande = $hMsOrders->get($id);
        if (!is_object($commande)) {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_ERROR_15, $baseurl);
        }
        $msg                 = [];
        $msg['NUM_COMMANDE'] = $id;
        $registry            = new Myservices\RegistryFile();
        $texts               = $qualityLinks = [];
        $texts               = $hMsOrders->validateOrder($id, $qualityLinks);
        if (count($texts) > 0) {
            $msg['SUPPLEMENTAL'] = implode("\n", $texts);
        } else {
            $msg['SUPPLEMENTAL'] = '';
        }
        $msg['ANNULATION']   = $registry->getfile(MYSERVICES_TEXTFILE1) . "\n\n" . sprintf(_MYSERVICES_CANCEL_DURATION,\XoopsModules\Myservices\Utilities::getModuleOption('maxdelaycancel'));
        $msg['QUALITY']      = $registry->getfile(MYSERVICES_TEXTFILE4) . "\n" . implode("\n", $qualityLinks);
        $msg['SUPPLEMENTAL'] =\XoopsModules\Myservices\Utilities::textForEmail($msg['SUPPLEMENTAL']);
        $msg['ANNULATION']   =\XoopsModules\Myservices\Utilities::textForEmail($msg['ANNULATION']);
        $msg['QUALITY']      =\XoopsModules\Myservices\Utilities::textForEmail($msg['QUALITY']);
       \XoopsModules\Myservices\Utilities::sendEmailFromTpl('command_shop_verified.tpl',\XoopsModules\Myservices\Utilities::getEmailsFromGroup(\XoopsModules\Myservices\Utility::getModuleOption('grp_sold')), _MYSERVICES_PAYPAL_VALIDATED, $msg);
       \XoopsModules\Myservices\Utilities::sendEmailFromTpl('command_client_verified.tpl', $commande->getVar('orders_email'), _MYSERVICES_PAYPAL_VALIDATED, $msg);
        break;

    // ****************************************************************************************************************
    case 'texts':    // Texts Management
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(7);
        // require_once MYSERVICES_PATH . 'class/RegistryFile.php';
        $registry = new Myservices\RegistryFile();

        $sform = new \XoopsThemeForm(_MI_MYSERVICES_ADMENU8, 'frmatxt', $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'savetexts'));
        // Cancellation
        $editor1 =\XoopsModules\Myservices\Utilities::getWysiwygForm(_AM_MYSERVICES_CANCEL, 'text1', $registry->getfile(MYSERVICES_TEXTFILE1), 5, 60, 'hometext1_hidden');
        if ($editor1) {
            $sform->addElement($editor1, false);
        }
        // How to order off period?
        $editor2 =\XoopsModules\Myservices\Utilities::getWysiwygForm(_MYSERVICES_NOTIN_HOURS, 'text2', $registry->getfile(MYSERVICES_TEXTFILE2), 5, 60, 'hometext2_hidden');
        if ($editor2) {
            $sform->addElement($editor2, false);
        }
        // Terms
        $editor3 =\XoopsModules\Myservices\Utilities::getWysiwygForm(_AM_MYSERVICES_CGV, 'text3', $registry->getfile(MYSERVICES_TEXTFILE3), 5, 60, 'hometext3_hidden');
        if ($editor3) {
            $sform->addElement($editor3, false);
        }

        // Quality Form
        $editor4 =\XoopsModules\Myservices\Utilities::getWysiwygForm(_AM_MYSERVICES_QUALITY, 'text4', $registry->getfile(MYSERVICES_TEXTFILE4), 5, 60, 'hometext4_hidden');
        if ($editor4) {
            $sform->addElement($editor4, false);
        }

        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', _AM_MYSERVICES_MODIFY, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform =\XoopsModules\Myservices\Utilities::formMarkRequiredFields($sform);
        $sform->display();
        break;

    // ****************************************************************************************************************
    case 'savetexts':        // Save text
        // ****************************************************************************************************************
        // require_once MYSERVICES_PATH . 'class/RegistryFile.php';
        $registry = new Myservices\RegistryFile();
        $registry->savefile($myts->stripSlashesGPC($_POST['text1']), MYSERVICES_TEXTFILE1);
        $registry->savefile($myts->stripSlashesGPC($_POST['text2']), MYSERVICES_TEXTFILE2);
        $registry->savefile($myts->stripSlashesGPC($_POST['text3']), MYSERVICES_TEXTFILE3);
        $registry->savefile($myts->stripSlashesGPC($_POST['text4']), MYSERVICES_TEXTFILE4);
       \XoopsModules\Myservices\Utilities::updateCache();
       \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl, 2);
        break;

    // ****************************************************************************************************************
    case 'timesheet':    // Working hours
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(8);
       \XoopsModules\Myservices\Utilities::htitle(_MI_MYSERVICES_ADMENU9, 4);
        echo _AM_MYSERVICES_TIMESHEET_HLP . '<br><br>';
        $item  = null;
        $item  = $hMsPrefs->getPreference();
        $sform = new \XoopsThemeForm(_AM_MYSERVICES_WORK_HOURS, 'frmworkinghours', $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'saveedithours'));
        $sform->addElement(new \XoopsFormHidden('prefs_id', $item->getVar('prefs_id')));
        require_once XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/calendar.php';
        $jours = [_CAL_MONDAY, _CAL_TUESDAY, _CAL_WEDNESDAY, _CAL_THURSDAY, _CAL_FRIDAY, _CAL_SATURDAY, _CAL_SUNDAY];
        for ($i = 1; $i <= 7; ++$i) {
            $nom1 = sprintf('prefs_j%dt1debut', $i);
            $nom2 = sprintf('prefs_j%dt1fin', $i);
            $nom3 = sprintf('prefs_j%dt2debut', $i);
            $nom4 = sprintf('prefs_j%dt2fin', $i);
            $ind  = $i - 1;
            OneDay($ind, $item->getVar($nom1), $item->getVar($nom2), $item->getVar($nom3), $item->getVar($nom4), $sform);
        }
        $button_tray = new \XoopsFormElementTray('', '');
        $submit_btn  = new \XoopsFormButton('', 'post', _AM_MYSERVICES_MODIFY, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform =\XoopsModules\Myservices\Utilities::formMarkRequiredFields($sform);
        $sform->display();
        break;

    // ****************************************************************************************************************
    case 'saveedithours':    // Save work schedules
        // ****************************************************************************************************************
        xoops_cp_header();
        $id         = \Xmf\Request::getInt('prefs_id', 0, 'POST');
        $opRedirect = 'dashboard';
        if (!empty($id)) {
            $edit = true;
            $item = $hMsPrefs->get($id);
            if (!is_object($item)) {
               \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_NOT_FOUND, $baseurl, 5);
            }
            $item->unsetNew();
        } else {
            $item = $hMsPrefs->create(true);
        }

        $item->setVars($_POST);
        // Sauvegarde des heures par jour
        for ($i = 1; $i <= 7; ++$i) {
            for ($j = 1; $j <= 2; ++$j) {
                // Zone d�but
                $nom1 = sprintf('j%dt%ddebut1', $i, $j);
                $nom2 = sprintf('j%dt%ddebut2', $i, $j);
                // Zone fin
                $nom3 = sprintf('j%dt%dfin1', $i, $j);
                $nom4 = sprintf('j%dt%dfin2', $i, $j);
                // Zones de la classe
                $nom5 = sprintf('prefs_j%dt%ddebut', $i, $j);
                $nom6 = sprintf('prefs_j%dt%dfin', $i, $j);

                if (isset($_POST[$nom1]) && isset($_POST[$nom2])) {
                    if (0 == \Xmf\Request::getInt($nom1, 0, 'POST') && 0 == \Xmf\Request::getInt($nom2, 0, 'POST')) {
                        $valdeb = '00:00:00';
                    } else {
                        $valdeb = sprintf('%02d:%02d:00', \Xmf\Request::getInt($nom1, 0, 'POST'), \Xmf\Request::getInt($nom2, 0, 'POST'));
                    }
                }

                if (isset($_POST[$nom3]) && isset($_POST[$nom4])) {
                    if (0 == \Xmf\Request::getInt($nom3, 0, 'POST') && 0 == \Xmf\Request::getInt($nom4, 0, 'POST')) {
                        $valfin = '00:00:00';
                    } else {
                        $valfin = sprintf('%02d:%02d:00', \Xmf\Request::getInt($nom3, 0, 'POST'), \Xmf\Request::getInt($nom4, 0, 'POST'));
                    }
                }
                $item->setVar($nom5, $valdeb);
                $item->setVar($nom6, $valfin);
            }
        }
        $res = $hMsPrefs->insert($item);
        if ($res) {
           \XoopsModules\Myservices\Utilities::updateCache();
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_OK, $baseurl . '?op=' . $opRedirect, 2);
        } else {
           \XoopsModules\Myservices\Utilities::redirect(_AM_MYSERVICES_SAVE_PB, $baseurl . '?op=' . $opRedirect, 5);
        }
        break;

    // ****************************************************************************************************************
    case 'default':
    case 'dashboard':
        // ****************************************************************************************************************
        xoops_cp_header();
        // myservices_adminMenu(0);
       \XoopsModules\Myservices\Utilities::htitle(_AM_MYSERVICES_LAST_ORDERS, 4);
        $itemsCount = 15;    // Number of items to display
        // Display the last orders validated x
        $start       = \Xmf\Request::getInt('start', 0, 'GET');
        $tblCommands = [];

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('orders_id', 0, '<>'));
        $criteria->add(new \Criteria('orders_state', MYSERVICES_ORDER_VALIDATED, '='));
        $commandsCount = $hMsOrders->getCount($criteria);    // Find the total number of orders
        $pagenav       = new \XoopsPageNav($commandsCount, $itemsCount, $start, 'start', 'op=dashboard');
        $criteria->setSort('orders_date');
        $criteria->setOrder('DESC');
        $criteria->setLimit($itemsCount);
        $criteria->setStart($start);
        $tblCommands = $hMsOrders->getObjects($criteria);

        $class = '';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        foreach ($tblCommands as $commande) {
            $class = ('even' === $class) ? 'odd' : 'even';
            // Find the elements of the order (the basket)
            $critePanier = new \Criteria('caddy_orders_id', $commande->getVar('orders_id'), '=');
            $tblCaddy    = [];
            $tblCaddy    = $hMsCaddy->getObjects($critePanier);
            echo "<tr class='" . $class . "'>\n";
            echo "<td colspan='5'><b>&raquo;</b> "
                 .\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($commande->getVar('orders_date'))
                 . ' - '
                 . $commande->getVar('orders_lastname')
                 . ' '
                 . $commande->getVar('orders_firstname')
                 . ' - '
                 . $commande->getVar('orders_address')
                 . ' '
                 . $commande->getVar('orders_zip')
                 . ' '
                 . $commande->getVar('orders_town')
                 . ' - '
                 . $commande->getVar('orders_telephone')
                 . ' ('
                 . $commande->getVar('orders_email')
                 . ')'
                 . "</td>\n";
            echo "</tr>\n";
            foreach ($tblCaddy as $caddy) {
                $employee = $product = null;
                $employee = $hMsEmployees->get($caddy->getVar('caddy_employees_id'));
                $product  = $hMsProducts->get($caddy->getVar('caddy_products_id'));
                echo "<tr>\n";
                echo "<td class='even' align='center'>" .\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($caddy->getVar('caddy_start')) . "</td>\n";
                echo "<td class='odd' align='center'>" .\XoopsModules\Myservices\Utilities::sqlDateTimeToFrench($caddy->getVar('caddy_end')) . "</td>\n";
                echo "<td class='even'>" . $product->getVar('products_title') . "</td>\n";
                echo "<td class='odd'>" . $employee->getEmployeeFullName() . "</td>\n";
                echo "<td class='even' align='right'>" . $myservices_Currency->amountForDisplay($caddy->getVar('caddy_price')) . "</td>\n";
                echo "</tr>\n";
            }
            unset($critePanier);
        }
        echo '</table>';
        echo "<div align='right'>" . $pagenav->renderNav() . '</div>';
        require_once __DIR__ . '/admin_footer.php';  //show_footer();
        break;

}
//xoops_cp_footer();
require_once __DIR__ . '/admin_footer.php';
