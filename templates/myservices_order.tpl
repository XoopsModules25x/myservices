<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<h2><{$smarty.const._MYSERVICES_VALIDATE_CMD}></h2>
<br>
<{$form}>
<{if $op == 'paypal'}>
    <br>
    <div align="center"><b><{$smarty.const._MYSERVICES_DETAILS_EMAIL}></b></div>
    <br>
<{else}>
    <br>
    <b><{$smarty.const._MYSERVICES_REQUIRED}></b>
<{/if}>
