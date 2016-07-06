<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 24 nov. 07 at 16:17:40
 * ****************************************************************************
 */

/**
 * Fenêtre affichée sous la forme d'un popup qui renseigne l'utilisateur sur la méthode à utiliser pour réserver un service en dehors de la période normale de travail
 */
require_once __DIR__ . '/header.php';
error_reporting(0);
@$xoopsLogger->activated = false;
require_once MYSERVICES_PATH . 'class/registryfile.php';
xoops_header(false);
$registry = new myservices_registryfile();
echo $registry->getfile(MYSERVICES_TEXTFILE2);
echo '<div style="text-align:center;"><input class="formButton" value="' . _CLOSE . '" type="button" onclick="javascript:window.close();" /></div>';
xoops_footer();
