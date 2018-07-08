<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<h3><{$smarty.const._MYSERVICES_EMPLOYEES_LIST}></h3>
<br>

<{if count($employees) >0 }>
    <ul>
        <{foreach item=employee from=$employees}>
            <li><a href="<{$employee.employees_link}>"><{$employee.employees_fullname}></a></li>
        <{/foreach}>
    </ul>
<{else}>
    <h4><{$smarty.const._MYSERVICES_NO_EMPLOYEES}></h4>
<{/if}>
