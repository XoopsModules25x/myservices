<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<{if $success }>
    <h3><{$smarty.const._MYSERVICES_THANK_YOU}></h3>
    <br>
    <br>
    <h4><{$smarty.const._MYSERVICES_TRANSACTION_FINSIHED}></h4>
<{else}>
    <h3><{$smarty.const._MYSERVICES_PAYPAL_FAILED}></h3>
<{/if}>
<br>
<br>
<a href="<{$smarty.const.MYSERVICES_URL}>"><{$smarty.const._MYSERVICES_CONTINUE_SHOPPING}></a>
