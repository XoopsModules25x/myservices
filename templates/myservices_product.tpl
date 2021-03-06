<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<{$breadcrumb}>
<br>
<br>
<div style="text-align: justify;">
    <div class="imageSlideshowHolder" id="imageSlideshowHolder">
        <img src="<{$product.products_image_url1}>" alt="" border="0">
        <img src="<{$product.products_image_url2}>" alt="" border="0">
        <img src="<{$product.products_image_url3}>" alt="" border="0">
        <img src="<{$product.products_image_url4}>" alt="" border="0">
        <img src="<{$product.products_image_url5}>" alt="" border="0">
        <img src="<{$product.products_image_url6}>" alt="" border="0">
        <img src="<{$product.products_image_url7}>" alt="" border="0">
        <img src="<{$product.products_image_url8}>" alt="" border="0">
        <img src="<{$product.products_image_url9}>" alt="" border="0">
        <img src="<{$product.products_image_url10}>" alt="" border="0">
    </div>
    <h2><{$product.products_title}></h2>
    <br>
    <{$product.products_summary}><br><{$product.products_description}>
    <br><br><{$smarty.const._MYSERVICES_DURATION}> : <span style="color: #C80000; font-weight: bold;"><{$product.products_duration}></span>
    <br><{$smarty.const._MYSERVICES_PRODUCT_PRICETTC}> : <span style="color: #C80000; font-weight: bold;"><{$product.products_displayshort_pricettc}></span>
</div>

<script type="text/javascript">
    initImageGallery('imageSlideshowHolder');
</script>

<br>
<{if $no_employees != ''}>
    <{$no_employees}>
<{else}>
    <hr size="1" noshade="noshade" style="width: 80%; height: 1px;">
    <form method="post" action="<{$smarty.const.MYSERVICES_URL}>cart.php" name="frmresa" id="frmresa" onsubmit='return verifyParameters();'>
        <{securityToken}><{*//mb*}>
        <table border="0">
            <tr>
                <td>
                    <table border="0">
                        <tr>    <{* Sélecteur de personne *}>
                            <td><b><{$smarty.const._MYSERVICES_EMPLOYEE}></b></td>
                            <td><select name="employee" id="employee" onchange="updateEmployee($F('employee'));"><{html_options options=$employeesSelect selected=$selectedEmployee}></select></td>
                        </tr>
                        <tr>    <{* Sélecteur de date *}>
                            <td><b><{$smarty.const._MYSERVICES_DATE}></b></td>
                            <td><select name="month" id="month" onchange="updateCalendar($F('year'), $F('month'));"><{html_options options=$monthNames selected=$month}></select> <select name="year" id="year"
                                                                                                                                                                                          onchange="updateCalendar($F('year'), $F('month'));"><{html_options options=$yearsSelect selected=$currentYear}></select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="2"> <{* Informations sur la personne sélectionnée *}>
                    <{if isset($currentEmployee)}>
                        <div id="infoEmployee" style="height: 150px; margin-left: 10px; overflow: auto; text-align: justify;">
                            <{include file="db:myservices_curemployee.tpl"}>
                        </div>
                    <{/if}>
                </td>
            </tr>
            <tr>
                <td align="center"> <{* Calendrier calculé *}>
                    <div id="dynCalendar">
                        <b><{$smarty.const._MYSERVICES_SELECT_DAY}></b> *<br>
                        <{$calendar}>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table border="0" style="">
                        <tr>    <{* Sélection de l'heure de début et de la durée *}>
                            <td align="right"><b><span id="selectedDate"></span>&nbsp; <{$smarty.const._MYSERVICES_STARTING_HOUR}> <select name="selectedTime" id="selectedTime"><{html_options options=$timeSelect}></select></b></td>
                            <td align="left">&nbsp;<b><{$smarty.const._MYSERVICES_DURATION_SEL}></b> <select name="duration" id="duration"><{html_options options=$durationSelect}></select></td>
                        </tr>
                    </table>
                    <br>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <div id="submitPlace">  <{* Espace dynamique pour gérer la vérification de la disponibilité de la personne *}>
                        <{include file="db:myservices_availability.tpl"}>
                    </div>
                </td>
            </tr>
        </table>
        <input type="hidden" name="products_id" id="products_id" value="<{$product.products_id}>">
        <input type="hidden" name="op" id="op" value="add">
        <input type="hidden" name="selectedDay" id="selectedDay" value="">
        <input type="hidden" name="selectedMonth" id="selectedMonth" value="">
        <input type="hidden" name="selectedYear" id="selectedYear" value="">
    </form>
    <script type="text/javascript">
        <{* Mise à jour des champs cachés pour la sélection de la date *}>
        function selectDate(selYear, selMonth, selDay) {
            xoopsSetElementProp('selectedDay', 'value', selDay);
            xoopsSetElementProp('selectedMonth', 'value', selMonth);
            xoopsSetElementProp('selectedYear', 'value', selYear);
            Element.update('selectedDate', selDay + '/' + selMonth + '/' + selYear);
        }

        <{* AJAX - Mise à jour dynamique de la zone qui liste l'employé()e sélectionné(e) *}>
        function updateEmployee(idEmployee) {   <{* idEmployee = employé(e) sélectionné(e) *}>
            var pars1 = 'op=employee&idEmployee=' + idEmployee;
            var myAjax1 = new Ajax.Updater('infoEmployee', '<{$smarty.const.MYSERVICES_URL}>ajax.php', {method: 'post', parameters: pars1, encoding: '<{$xoops_charset}>'});
        }

        <{* AJAX - Mise à jour dynamique du calendrier *}>
        function updateCalendar(selYear, selMonth) {    <{* qlayer=ID du div sur lequel on travaille, selyear = année choisie, selmonth = mois sélectionné *}>
            var pars2 = 'op=calendar&year=' + selYear + '&month=' + selMonth;
            var myAjax2 = new Ajax.Updater('dynCalendar', '<{$smarty.const.MYSERVICES_URL}>ajax.php', {method: 'post', parameters: pars2, encoding: '<{$xoops_charset}>'});
        }

        <{* AJAX - Mise à jour de la zone de validation du formulaire et vérifications diverses *}>
        function verifyParameters() {
            if ($F('selectedDay') == '' || $F('selectedMonth') == '' || $F('selectedYear') == '') {
                alert('<{$smarty.const._MYSERVICES_ERROR13}>');
                Field.activate('month');
                return false;
            }
            var pars3 = 'op=availability&products_id=' + $F('products_id') + '&month=' + $F('selectedMonth') + '&year=' + $F('selectedYear') + '&day=' + $F('selectedDay') + '&employee_id=' + $F('employee') + '&duration=' + $F('duration') + '&time=' + $F('selectedTime');
            var myAjax3 = new Ajax.Updater('submitPlace', '<{$smarty.const.MYSERVICES_URL}>ajax.php', {method: 'post', parameters: pars3, evalScripts: true, encoding: '<{$xoops_charset}>'});
            return true;
        }
    </script>
    <br>
    <div align="center">
        <b><a href="javascript:openWithSelfMain('<{$smarty.const.MYSERVICES_URL}>out-of-period.php','',640,480);"><{$smarty.const._MYSERVICES_OUT_OF_PERIOD}></a></b>
        <br>* <i><{$help}></i>
    </div>
<{/if}>
