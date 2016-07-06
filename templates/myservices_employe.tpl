<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<a href="<{$smarty.const.MYSERVICES_URL}>"><{$moduleName}></a> &raquo; <{$employee.employes_lastname}> <{$employee.employes_firstname}>
<br><br>
<div style="margin-left: 10px; text-align: justify;">
    <{if $employee.employes_photo1 != ''}>
        <div style="margin: 5px 10px;float: left;">
            <img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employes_photo1}>" alt="" border="0"/>
        </div>
    <{/if}>
    <h3><{$employee.employes_lastname}> <{$employee.employes_firstname}></h3>
    <br><{$employee.employes_bio}>
</div>
<br>
<table border="0">
    <tr>
        <{if $employee.employes_photo2 != ''}>
            <td><img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employes_photo2}>" alt="" border="0"/></td>
        <{/if}>
        <{if $employee.employes_photo3 != ''}>
            <td><img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employes_photo3}>" alt="" border="0"/></td>
        <{/if}>
        <{if $employee.employes_photo4 != ''}>
            <td><img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employes_photo4}>" alt="" border="0"/></td>
        <{/if}>
        <{if $employee.employes_photo5 != ''}>
            <td><img src="<{$smarty.const.XOOPS_UPLOAD_URL}>/<{$employee.employes_photo5}>" alt="" border="0"/></td>
        <{/if}>
    </tr>
</table>

<{if count($products) >0 }>
    <br>
    <hr/>
    <h4><{$smarty.const._MYSERVICES_SERVICES_BY_ME}> <{$employee.employes_lastname}> <{$employee.employes_firstname}> :</h4>
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
