<?php
/**
 * ****************************************************************************
 * myservices - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * Created on 24 nov. 07 at 17:57:04
 * ****************************************************************************
 */

/**
 * Script AJAX chargé de mettre à jour la partie calendrier et employé en fonction des choix de l'utilisateur
 * Le script vérifie aussi la disponibilité de la personner choisie pour le produit sélectionné et pour une date précisée.
 */
require_once __DIR__ . '/header.php';
error_reporting(0);
@$xoopsLogger->activated = false;

$op       = isset($_POST['op']) ? $_POST['op'] : '';
$resultat = '';

switch ($op) {
	case 'employee':	// Réaffichage des informations de l'employé(e) sélectionné(e) dans la liste déroulante
        $idEmployee = isset($_POST['idEmployee']) ? (int)$_POST['idEmployee'] : 0;
        if ($idEmployee > 0) {
            $employee = null;
            $employee = $hMsEmployes->get($idEmployee);
            if (is_object($employee)) {
                require_once XOOPS_ROOT_PATH . '/class/template.php';
                $xoopsTpl = new XoopsTpl();
                $xoopsTpl->assign('currentEmployee', $employee->toArray());
                $resultat = $xoopsTpl->fetch('db:myservices_curemployee.tpl');
            }
        }
        break;

	case 'calendar':	// Réaffichage du calendrier en fonction du mois et de l'année sélectionnés
        $year  = isset($_POST['year']) ? (int)$_POST['year'] : 0;
        $month = isset($_POST['month']) ? (int)$_POST['month'] : 0;
        if ($year > 0 && $month > 0) {
            require_once MYSERVICES_PATH . 'class/activecalendar.php';
            require_once XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/calendar.php';
            require_once MYSERVICES_PATH . 'configs.php';
            $prefMagasin = $hMsPrefs->getPreference();
            $monthNames  = array(1 => _CAL_JANUARY, 2 => _CAL_FEBRUARY, 3 => _CAL_MARCH, 4 => _CAL_APRIL, 5 => _CAL_MAY, 6 => _CAL_JUNE, 7 => _CAL_JULY, 8 => _CAL_AUGUST, 9 => _CAL_SEPTEMBER, 10 => _CAL_OCTOBER, 11 => _CAL_NOVEMBER, 12 => _CAL_DECEMBER);
            $calendar    = new activeCalendar($year, $month);
            $calendar->setMonthNames(array_values($monthNames));
			// Récupération du nom des jours, raccourcis
            $l     = $prefs['daysLength'];    // Longueur du texte des jours
            $jours = array(
                substr(_CAL_SUNDAY, 0, $l),
                substr(_CAL_MONDAY, 0, $l),
                substr(_CAL_TUESDAY, 0, $l),
                substr(_CAL_WEDNESDAY, 0, $l),
                substr(_CAL_THURSDAY, 0, $l),
                substr(_CAL_FRIDAY, 0, $l),
                substr(_CAL_SATURDAY, 0, $l)
            );

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
				// Premier test, on regarde si le jour tombe sur un jour normal de fermeture, deuxième test, on regarde si on tombe sur un jour de fermeture exceptionnelle
                if (in_array($weekDay, $closedDays) || in_array($i, $ExceptionnalclosedDays)) {
                    $calendar->setEvent($year, $month, $i, 'event');
                } else {
                    $timestamp = mktime(23, 59, 59, $month, $i, $year);
                    $show      = true;
					// Vérification par rapport au temps de latence
                    if ($year == $nowEntities['year'] && $month == $nowEntities['mon']) {    // On est sur le mois et l'ann�e courante
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

            $resultat = $calendar->showMonth();
        }
        break;

	case 'availability':	// Vérifie la disponibilité de la personne pour le produit et la date sélectionnés
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $products_id = isset($_POST['products_id']) ? (int)$_POST['products_id'] : 0;
        $month       = isset($_POST['month']) ? (int)$_POST['month'] : 0;
        $year        = isset($_POST['year']) ? (int)$_POST['year'] : 0;
        $day         = isset($_POST['day']) ? (int)$_POST['day'] : 0;
        $employee_id = isset($_POST['employee_id']) ? (int)$_POST['employee_id'] : 0;
        $duration    = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;
        $time        = isset($_POST['time']) ? $_POST['time'] : '';
        $xoopsTpl    = new XoopsTpl();

        if (empty($products_id) || empty($month) || empty($year) || empty($day) || empty($employee_id) || empty($duration) || empty($time)) {
            $xoopsTpl->assign('additionalBefore', '<b>' . _MYSERVICES_ERROR14 . '</b><br>');
		} else {	// Les paramètres attendus sont présents
			// 1) Est-ce que le délai entre maintenant et la date souhaitée est supérieur au temps de latence ?
            if (!$hMsPrefs->canOrderNow($year, $month, $day, $time)) {
                $xoopsTpl->assign('additionalBefore', '<b>' . sprintf(_MYSERVICES_ERROR17, myservices_utils::getModuleOption('latence')) . '</b><br>');
            } else {
				// 2) Vérification que le magasin est bien ouvert ce jour là
                if (!$hMsPrefs->isStoreOpen($year, $month, $day, $time)) {
                    $xoopsTpl->assign('additionalBefore', '<b>' . _MYSERVICES_ERROR15 . '</b><br>');
                } else {
					// 3) Vérification fermeture exceptionnelle
                    if ($hMsCalendar->isStoreClosed($year, $month, $day)) {
                        $xoopsTpl->assign('additionalBefore', '<b>' . _MYSERVICES_ERROR16 . '</b><br>');
                    } else {
						// Arrivé là, il ne reste plus qu'à vérifier la disponibilité de la personne
                        if (!$hMsEmployes->isEmployeeAvailable($year, $month, $day, $time, $duration, $products_id, $employee_id)) {
                            $xoopsTpl->assign('additionalBefore', '<b>' . _MYSERVICES_ERROR18 . '</b><br>');
                        } else {
                            $contentOk = '';
                            $contentOk .= '<b>' . _MYSERVICES_AVAILABILITY_OK . '</b><br>';
                            $contentOk .= "<input type='submit' name='btnsubmit' id='btnsubmit' value='" . _MYSERVICES_RESERVE . "' />";
                            $xoopsTpl->assign('additionalAfter', $contentOk);
                            //$xoopsTpl->assign('additionalBefore', 'products_id='.$products_id.' month='.$month.' year='.$year.' day='.$day.' employee_id='.$employee_id.' duration='.$duration.' time='.$time);
                        }
                    }
                }
            }
        }
        $resultat = $xoopsTpl->fetch('db:myservices_availability.tpl');
        break;
}
echo utf8_encode($resultat);
