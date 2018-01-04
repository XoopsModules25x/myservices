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
 * Bloc, Liste des salariés actifs
 * @param $options
 * @return array
 */
function b_ms_employes_show($options)
{
    // Options = Nombre d'éléments visibles simultanément dans la liste
    require XOOPS_ROOT_PATH . '/modules/myservices/include/common.php';
    $block      = [];
    $itemsCount = (int)$options[0];
    $tblItems   = [];
    $select     = '';
    $jump       = MYSERVICES_URL . 'employee.php?employes_id=';
    $additional = " onchange='location=\"" . $jump . "\"+this.options[this.selectedIndex].value'";
    $additional .= " style='width: 170px; max-width: 170px;'";
    $select     .= "<select name='blockSelectEmployee' id='blockSelectEmployee' size='" . $itemsCount . "'" . $additional . '>';
    $tblItems   = $hMsEmployes->getActiveEmployees();
    foreach ($tblItems as $employee) {
        $select .= "<option value='" . $employee->getVar('employes_id') . "'>" . xoops_trim($employee->getVar('employes_lastname')) . ' ' . xoops_trim($employee->getVar('employes_firstname')) . '</option>';
    }
    $select .= '</select>';

    $block['block_select'] = $select;

    return $block;
}

function b_ms_employes_edit($options)
{
    // Options = Nombre d'éléments visibles simultanément dans la liste
    $form = '';
    $form .= _MB_MYSERVICES_NBELTS_INLIST . " <input type='text' name='options[]' value='" . $options[0] . "'><br>";

    return $form;
}

/**
 * Ad hoc Block
 * @param $options
 */
function b_ms_employes_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = &b_ms_employes_show($options);
    $tpl     = new XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:myservices_block_employes.tpl');
}
