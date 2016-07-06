<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<h3><{$smarty.const._MI_MYSERVICES_SMNAME2}></h3>
<br>
<{if $emptyCart}>
    <i><{$smarty.const._MYSERVICES_CART_IS_EMPTY}></i>
<{else}>
    <form method="post" name="frmUpdate" id="frmUpdate" action="<{$smarty.const.MYSERVICES_URL}>cart.php" style="margin:0; padding:0; border: 0; display: inline;">
        <table cellspacing="0">
            <tr>
                <th><{$smarty.const._MYSERVICES_PRODUCT}></th>
                <th><{$smarty.const._MYSERVICES_DATE_DURATION}></th>
                <th><{$smarty.const._MYSERVICES_PRODUCT_PRICE_UNIT}></th>
                <th><{$smarty.const._MYSERVICES_AMOUNT_HT}></th>
                <th><{$smarty.const._MYSERVICES_VAT}></th>
                <th colspan="2"><{$smarty.const._MYSERVICES_PRODUCT_PRICETTC}></th>
            </tr>
            <{foreach item=product from=$caddieProducts}>
                <tr>
                    <td><a href="<{$product.products_url}>" title="<{$product.products_href_title}>"><{$product.products_title}></a></td>
                    <td align='center'><{$product.products_reserved_date}><br><{$product.products_reserved_time}><br><{$product.products_reserved_duration}> <{$smarty.const._MYSERVICES_HOURS}><br><a href="<{$product.employee.employes_link}>"
                                                                                                                                                                                                       title="<{$product.employee.employes_href_title}>"><{$product.employee.employes_fullname}></a></td>
                    <td align='right'><{$product.products_displaylong_price}></td>
                    <td align='right'><{$product.products_amount_ht}></td>
                    <td align='right'><{$product.products_vat_amount}> (<{$product.products_vat_rate}> %)</td>
                    <td align='right'><{$product.products_price_ttc}></td>
                    <td align='right'>&nbsp;<a href="<{$smarty.const.MYSERVICES_URL}>cart.php?op=delete&products_id=<{$product.products_number}>" <{$confirm_delete_item}> title="<{$smarty.const._MYSERVICES_REMOVE_ITEM}>"><img src="<{$smarty.const.MYSERVICES_IMAGES_URL}>cartdelete.gif"
                                                                                                                                                                                                                                  alt="<{$smarty.const._MYSERVICES_REMOVE_ITEM}>" border="0"/></td>
                </tr>
            <{/foreach}>
            <tr>
                <td colspan="3"><b><{$smarty.const._MYSERVICES_TOTAL}></b></td>
                <td align="right" valign="middle"><b><{$commandAmount}></b></td>
                <td align="right" valign="middle"><b><{$vatAmount}></b></td>
                <td colspan="2" align="center" valign="middle"><b><{$commandAmountTTC}></b></td>
            </tr>
            <tr>
                <td colspan="5">
                    <br>
    </form>
    <form method="post" name="frmEmpty" id="frmEmpty" action="<{$smarty.const.MYSERVICES_URL}>cart.php" <{$confEmpty}> style="margin:0; padding:0; border: 0; display: inline;">
        <input type="hidden" name="op" id="op" value="empty"/>
        <input type="submit" name="btnEmpty" id="btnEmpty" value="<{$smarty.const._MYSERVICES_EMPTY_CART}>"/>
    </form>
    <form method="post" name="frmGoOn" id="frmGoOn" action="<{$smarty.const.MYSERVICES_URL}>" style="margin:0; padding:0; border: 0; display: inline;">
        <input type="submit" name="btnGoOn" id="btnGoOn" value="<{$smarty.const._MYSERVICES_GO_ON}>"/>
    </form>
    </td>
    <td colspan="2" align="center">
        <br>
        <form method="post" name="frmCheckout" id="frmCheckout" action="<{$smarty.const.MYSERVICES_URL}>checkout.php" style="margin:0; padding:0; border: 0; display: inline;">
            <input type="submit" name="btnCheckout" id="btnCheckout" value="<{$smarty.const._MYSERVICES_CHECKOUT}>"/>
        </form>
    </td>
    </tr>
    </table>
    <br>
<{/if}>
