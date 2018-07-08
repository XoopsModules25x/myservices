<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<a href="<{$smarty.const.MYSERVICES_URL}>"><{$moduleName}></a> &raquo; <{$employee.employees_lastname}> <{$employee.employees_firstname}>
<br><br>
<div style="margin-left: 10px; text-align: justify;">
    <{if $employee.employees_photo1 != ''}>
        <div style="margin: 5px 10px;float: left;">
            <img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employees_photo1}>" alt="" border="0">
        </div>
    <{/if}>
    <h3><{$employee.employees_lastname}> <{$employee.employees_firstname}></h3>
    <br><{$employee.employees_bio}>
</div>
<br>
<table border="0">
    <tr>
        <{if $employee.employees_photo2 != ''}>
            <td><img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employees_photo2}>" alt="" border="0"></td>
        <{/if}>
        <{if $employee.employees_photo3 != ''}>
            <td><img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employees_photo3}>" alt="" border="0"></td>
        <{/if}>
        <{if $employee.employees_photo4 != ''}>
            <td><img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employees_photo4}>" alt="" border="0"></td>
        <{/if}>
        <{if $employee.employees_photo5 != ''}>
            <td><img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employees_photo5}>" alt="" border="0"></td>
        <{/if}>
    </tr>
</table>

<{if count($products) >0 }>
    <br>
    <hr>
    <h4><{$smarty.const._MYSERVICES_SERVICES_BY_ME}> <{$employee.employees_lastname}> <{$employee.employees_firstname}> :</h4>
    <ul>
        <{foreach item=product from=$products}>
            <li><a href="<{$product.products_url}>" title="<{$product.products_href_title}>"><{$product.products_title}></a></li>
        <{/foreach}>
    </ul>
<{/if}>
<br>
<div align="center">
    <a href="<{$smarty.const.MYSERVICES_URL}>employees.php"><{$smarty.const._MYSERVICES_SEE_LIST_EMPLOYEES}></a>
    <br><a href="<{$smarty.const.MYSERVICES_URL}>products.php"><{$smarty.const._MYSERVICES_SEE_ALL_PRODUCTS}></a>
</div>
<br>
