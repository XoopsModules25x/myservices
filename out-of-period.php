<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 24 nov. 07 at 16:17:40
 * ****************************************************************************
 */

/**
 * Fen�tre affich�e sous la forme d'un popup qui renseigne l'utilisateur sur la m�thode � utiliser pour r�server un service en dehors de la p�riode normale de travail
 */
require_once 'header.php';
error_reporting(0);
@$xoopsLogger->activated = false;
require_once MYSERVICES_PATH.'class/registryfile.php';
xoops_header(false);
$registry = new myservices_registryfile();
echo $registry->getfile(MYSERVICES_TEXTFILE2);
echo '<div style="text-align:center;"><input class="formButton" value="'._CLOSE.'" type="button" onclick="javascript:window.close();" /></div>';
xoops_footer();
?>