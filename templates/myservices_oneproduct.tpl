<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<div class="buy">
    <div style="margin: 5px 10px;float: left;">
        <a href="<{$product.products_url}>" title="<{$product.products_href_title}>"><img src="<{$product.products_image_url1}>" alt="<{$product.products_href_title}>"></a>
    </div>
    <br>
    <h4><{$product.products_title}></h4>
    <br>
    <{$product.products_summary}>
    <br><br><{$smarty.const._MYSERVICES_DURATION}> : <span style="color: #C80000; font-weight: bold;"><{$product.products_duration}></span><br><{$smarty.const._MYSERVICES_PRODUCT_PRICETTC}> : <span style="color: #C80000; font-weight: bold;"><{$product.products_displayshort_pricettc}></span>
    <br>
    <br style="line-height:15px;">
    <a href="<{$product.products_url}>" title="<{$smarty.const._MYSERVICES_SEE_PRODUCT}>"><img src="<{$smarty.const.MYSERVICES_IMAGES_URL}><{$smarty.const._MYSERVICES_RESERVER_PICTURE}>" alt="<{$smarty.const._MYSERVICES_SEE_PRODUCT}>" border="0"></a><br>
    <br style="line-height:30px;">
</div>
