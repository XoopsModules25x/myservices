<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<h2><{$smarty.const._MYSERVICES_LISTE}></h2>
<br>

<{foreach key=categoryId item=products from=$products}>
    <a href="<{$products[0].products_category.categories_url}>" title="<{$products[0].products_category.categories_href_title}>"><h3><{$products[0].products_category.categories_title}></h3></a>
    <ul>
        <{foreach item=product from=$products}>
            <li>
                <a href="<{$product.products_url}>" title="<{$product.products_href_title}>"><{$product.products_title}></a>
                <br><{$product.products_summary}>
            </li>
            <br>
        <{/foreach}>
    </ul>
    <hr/>
<{/foreach}>
