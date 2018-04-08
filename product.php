<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 20 oct. 07 at 14:38:20
 * ****************************************************************************
 */

/**
 * Affichage d'un produit
 */
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'myservices_product.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once MYSERVICES_PATH . 'class/activecalendar.php';

// Initialisations
$vatArray = $closedDays = $employees = $employeesSelect = $yearsSelect = [];

// Catégorie sélectionnée ***************************************************************
$currentProductId = \Xmf\Request::getInt('products_id', 0, 'GET');
if (0 == $currentProductId) {
    myservices_utils::redirect(_MYSERVICES_ERROR4, 'index.php', 5);
}

// Chargement du produit ****************************************************************
$currentProduct = null;
$currentProduct = $hMsProducts->get($currentProductId);
if (!is_object($currentProduct)) {    // Est ce que le produit existe ?
    myservices_utils::redirect(_MYSERVICES_ERROR3, 'index.php', 5);
}
// Est ce que le produit est en ligne ?
if (0 == $currentProduct->getVar('products_online')) {
    myservices_utils::redirect(_MYSERVICES_ERROR6, 'index.php', 5);
}

// Lecture des TVA **********************************************************************
$vatArray = $hMsVat->getItems();

// Formatage du produit courant *********************************************************
$xoopsTpl->assign('product', $currentProduct->toArray());

// Recherche de la catégorie du produit *************************************************
$productCategory = null;
$productCategory = $hMsCategories->get($currentProduct->getVar('products_categories_id'));
if (!is_object($productCategory)) {
    myservices_utils::redirect(_MYSERVICES_ERROR5, 'index.php', 5);
}
// Formatage de la catégorie courante ***************************************************
$xoopsTpl->assign('category', $productCategory->toArray());

// Breadcrumb ***************************************************************************
$xoopsTpl->assign('breadcrumb', $hMsCategories->getBreadCrumb($productCategory));

// Recherche des personnes qui fournissent ce service ***********************************
$employees = $hMsEmployes->getEmployeesForProduct($currentProductId);
if (0 == count($employees)) {    // Personne n'assure ce service !
    $xoopsTpl->assign('no_employees', _MYSERVICES_ERROR7);
} else {
    $xoopsTpl->assign('no_employees', '');
    foreach ($employees as $item) {
        $xoopsTpl->append('employees', $item->toArray());
        $employeesSelect[$item->getVar('employes_id')] = $item->getEmployeeFullName();
    }
    $xoopsTpl->assign('employeesSelect', $employeesSelect);
}
// Bulle d'aide pour la durée à attendre avant de passer commande
$xoopsTpl->assign('help', sprintf(_MYSERVICES_NO_COMMAND_BEFORE, myservices_utils::getModuleOption('latence')));

// Chargement du javascript de galerie d'images ****************************************
$urlCSS = MYSERVICES_URL . 'assets/css/product.css';
$css    = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$urlCSS\">";
$urlJS  = '<script type="text/javascript" src="' . MYSERVICES_URL . 'js/image-slideshow-4.js' . '"></script><script type="text/javascript" src="' . MYSERVICES_URL . 'js/prototype.js' . '"></script>';
$xoopsTpl->assign('xoops_module_header', $css . $urlJS);

// Calendrier ***************************************************************************
require_once XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/calendar.php';
require_once MYSERVICES_PATH . 'configs.php';
$prefMagasin = $hMsPrefs->getPreference();
$monthNames  = [1 => _CAL_JANUARY, 2 => _CAL_FEBRUARY, 3 => _CAL_MARCH, 4 => _CAL_APRIL, 5 => _CAL_MAY, 6 => _CAL_JUNE, 7 => _CAL_JULY, 8 => _CAL_AUGUST, 9 => _CAL_SEPTEMBER, 10 => _CAL_OCTOBER, 11 => _CAL_NOVEMBER, 12 => _CAL_DECEMBER];

$month       = \Xmf\Request::getInt('month', date('n'), 'GET');
$year        = \Xmf\Request::getInt('year', date('Y'), 'GET');
$employes_id = \Xmf\Request::getInt('employes_id', 0, 'GET');

if (0 == $employes_id) {    // Si aucun employé n'a été spécifié, on prend le premier
    if (count($employees) > 0) {
        $employee = array_slice($employees, 0, 1);
        $datas    = $employee[0]->toArray();
        $xoopsTpl->assign('currentEmployee', $datas);
    }
} else {
    if (isset($employees[$employes_id])) {
        $xoopsTpl->assign('currentEmployee', $employees[$employes_id]->toArray());
    }
}

// Création du contenu de la liste déroulante des années (N et N+1)
for ($i = date('Y'); $i <= date('Y') + 1; ++$i) {
    $yearsSelect[$i] = $i;
}
$xoopsTpl->assign('yearsSelect', $yearsSelect);
$xoopsTpl->assign('currentYear', $year);
$xoopsTpl->assign('monthNames', $monthNames);
$xoopsTpl->assign('month', $month);
$xoopsTpl->assign('year', $year);
$xoopsTpl->assign('selectedEmployee', $employes_id);

// Recherche des heures mini et maxi d'ouverture du magasin
$minHour = $hMsPrefs->getLowerOpenTime();
$maxHour = $hMsPrefs->getUpperOpenTime();

// Construction du sélecteur d'heures
$timeSelect   = [];
$heureDebut   = (int)substr($minHour, 0, 2);
$heureFin     = (int)substr($maxHour, 0, 2);
$minutesDebut = (int)substr($minHour, 3, 2);
$minutesFin   = (int)substr($maxHour, 3, 2);

for ($i = $heureDebut; $i <= $heureFin; ++$i) {
    $start = 0;
    $end   = 60;
    if ($i == $heureDebut) {
        $start = $minutesDebut;
    }
    if ($i == $heureFin) {
        $end = $minutesFin + 10;
    }
    for ($j = $start; $j < $end; $j += 10) {
        $value              = sprintf('%02d:%02d', $i, $j);
        $timeSelect[$value] = $value;
    }
}
$xoopsTpl->assign('timeSelect', $timeSelect);

$durationSelect = [];
for ($i = $currentProduct->getVar('products_duration'); $i <= $prefs['maxDuration']; ++$i) {
    $durationSelect[$i] = $i;
}
$xoopsTpl->assign('durationSelect', $durationSelect);

$calendar = new activeCalendar($year, $month);
$calendar->setMonthNames(array_values($monthNames));

// Récupération du nom des jours, raccourcis
$l     = $prefs['daysLength'];    // Longueur du texte des jours
$jours = [
    substr(_CAL_SUNDAY, 0, $l),
    substr(_CAL_MONDAY, 0, $l),
    substr(_CAL_TUESDAY, 0, $l),
    substr(_CAL_WEDNESDAY, 0, $l),
    substr(_CAL_THURSDAY, 0, $l),
    substr(_CAL_FRIDAY, 0, $l),
    substr(_CAL_SATURDAY, 0, $l)
];

$calendar->setDayNames($jours);

// Affichage des jours de fermeture
$daysMonth              = date('t', mktime(1, 1, 1, $month, 1, $year));    // Recherche du nombre de jours dans le mois (courant)
$closedDays             = $prefMagasin->getClosedDays();                // Jours normaux de fermeture du magasin
$ExceptionnalclosedDays = $hMsCalendar->getClosedDayInMonth($month, $year);    // Jours exceptionnels de fermeture dans le mois
$now                    = time();
$nowEntities            = getdate($now);
if ($year == $nowEntities['year'] && $month == $nowEntities['mon']) {    // On est sur le mois et l'année courante
    $firstUsableDay = $hMsPrefs->getFirstAvailableDayForOrder();    // Renvoie le premier jour à partir duquel on peut passer commande
}

for ($i = 1; $i <= $daysMonth; ++$i) {    // Boucle sur tous les jours du mois
    $weekDay = date('N', mktime(1, 1, 1, $month, $i, $year));    // Représentation numérique ISO-8601 du jour de la semaine (ajouté en PHP 5.1.0) de 1 (pour Lundi) à 7 (pour Dimanche)
    // Premier test, on regarde si le jour tombe sur un jour normal de fermeture, deuxi�me test, on regarde si on tombe sur un jour de fermeture exceptionnelle
    if (in_array($weekDay, $closedDays) || in_array($i, $ExceptionnalclosedDays)) {
        $calendar->setEvent($year, $month, $i, 'event');
    } else {
        $timestamp = mktime(23, 59, 59, $month, $i, $year);
        $show      = true;
        // Vérification par rapport au temps de latence
        if ($year == $nowEntities['year'] && $month == $nowEntities['mon']) {    // On est sur le mois et l'année courante
            if ($timestamp <= $firstUsableDay) {
                $show = false;
            }
        }

        //if ($show && $timestamp >= $now || ($year == $nowEntities['year']) && $month == $nowEntities['mon'] && $i == $nowEntities['mday']) {
        if ($show && $timestamp >= $now) {
            $calendar->setEvent($year, $month, $i, 'available', 'javascript:selectDate');
        }
    }
}
$xoopsTpl->assign('calendar', $calendar->showMonth());

// Titre de page et meta description ****************************************************
$pageTitle    = $productCategory->getVar('categories_title') . ' ' . $currentProduct->getVar('products_title') . ' - ' . myservices_utils::getModuleName();
$metaKeywords = myservices_utils::createMetaKeywords($currentProduct->getVar('products_title', 'e') . ' ' . $currentProduct->getVar('products_summary', 'e') . ' ' . $currentProduct->getVar('products_description', 'e'));
myservices_utils::setMetas($pageTitle, $pageTitle, $metaKeywords);
require_once XOOPS_ROOT_PATH . '/footer.php';
