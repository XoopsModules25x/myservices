<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * Version : $Id:
 * ****************************************************************************
 */

function myservices_search($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB;
	include XOOPS_ROOT_PATH.'/modules/myservices/include/common.php';
	include_once XOOPS_ROOT_PATH.'/modules/myservices/class/myservices_products.php';

	// Recherche dans les produits
	$sql = 'SELECT products_id, products_title FROM '.$xoopsDB->prefix('myservices_products').' WHERE (products_online = 1';
	$sql .= ') ';

	$tmpObject = new myservices_products();
	$datas = $tmpObject->getVars();
	$tblFields = array();
	$cnt = 0;
	foreach($datas as $key => $value) {
		if($value['data_type'] == XOBJ_DTYPE_TXTBOX || $value['data_type'] == XOBJ_DTYPE_TXTAREA) {
			if($cnt == 0) {
				$tblFields[] = $key;
			} else {
				$tblFields[] = ' OR '.$key;
			}
			$cnt++;
		}
	}

	$count = count($queryarray);
	$more = '';
	if( is_array($queryarray) && $count > 0 ) {
		$cnt = 0;
		$sql .= ' AND (';
		$more = ')';
		foreach($queryarray as $oneQuery) {
			$sql .= '(';
			$cond = " LIKE '%".$oneQuery."%' ";
			$sql .= implode($cond, $tblFields).$cond.')';
			$cnt++;
			if($cnt != $count) {
				$sql .= ' '.$andor.' ';
			}
		}
	}
	$sql .= $more.' ORDER BY products_title DESC';
	$i = 0;
	$ret = array();
	$myts =& MyTextSanitizer::getInstance();
	$result = $xoopsDB->query($sql,$limit,$offset);
 	while ($myrow = $xoopsDB->fetchArray($result)) {
		$ret[$i]['image'] = 'images/cartadd.gif';
		$ret[$i]['link'] = "product.php?products_id=".$myrow['products_id'];
		$ret[$i]['title'] = $myts->htmlSpecialChars($myrow['products_title']);
		$ret[$i]['time'] = time();
		$ret[$i]['uid'] = 1;
		$i++;
	}
	return $ret;
}
?>