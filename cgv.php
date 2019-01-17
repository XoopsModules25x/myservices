<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 30 nov. 07 at 12:21:54
 * ****************************************************************************
 */

use XoopsModules\Myservices;

/**
 * Affichage des CGV dans un popup
 */
require_once __DIR__ . '/header.php';
error_reporting(0);
@$xoopsLogger->activated = false;
// require_once MYSERVICES_PATH . 'class/RegistryFile.php';
xoops_header(false);
$registry = new Myservices\RegistryFile();
echo $registry->getfile(MYSERVICES_TEXTFILE3);
echo '<div style="text-align:center;"><input class="formButton" value="' . _CLOSE . '" type="button" onclick="javascript:window.close();"></div>';
xoops_footer();
