<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 30 nov. 07 at 12:21:54
 * ****************************************************************************
 */

 /**
  * Affichage des CGV dans un popup
  */
require_once 'header.php';
error_reporting(0);
@$xoopsLogger->activated = false;
require_once MYSERVICES_PATH.'class/registryfile.php';
xoops_header(false);
$registry = new myservices_registryfile();
echo $registry->getfile(MYSERVICES_TEXTFILE3);
echo '<div style="text-align:center;"><input class="formButton" value="'._CLOSE.'" type="button" onclick="javascript:window.close();" /></div>';
xoops_footer();
?>