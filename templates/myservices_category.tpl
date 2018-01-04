<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<{$breadcrumb}><br>
<{* Publicité de la catégorie *}>
<{if $category.categories_advertisement != '' }>
    <div align="center">
        <{$category.categories_advertisement}>
    </div>
<{/if}>

<{* Liste des sous-catégories *}>
<{if count($subCategories) >0 }>
    <hr size="1" noshade="noshade" style="width: 100%; height: 1px;">
    <table border='0' cellspacing='5' cellpadding='0' align="center">
        <tr>
            <{foreach item=onecategory from=$subCategories}>
            <td><a href="<{$onecategory.categories_url}>" title="<{$onecategory.categories_title}>"><b><{$onecategory.categories_title}></b></a></td>
            <{if $onecategory.count is div by 3}>
        </tr>
        <tr>
            <{/if}>
            <{/foreach}>
        </tr>
    </table>
    <hr size="1" noshade="noshade" style="width: 100%; height: 1px;">
<{/if}>

<{* Informations sur la catégorie courante *}>
<br>
<table border="0">
    <tr>
        <td>
            <div style="margin: 5px 10px 5px 5px;float: left;">
                <img src="<{$category.categories_fullimgurl}>" alt="<{$category.categories_href_title}>">
            </div>
            <h2><{$category.categories_title}></h2>
            <br>
            <{$category.categories_description}>
        </td>
    </tr>
    <tr>
        <{* Liste des produits *}>
        <{if count($products) >0 }>
            <td>
                <br>
                <h3><{$smarty.const._MYSERVICES_PRODUCTSOF_CATEGORY}></h3>
                <br>
                <table border='0' cellspacing='12' cellpadding='0' align="center">
                    <tr>
                        <{foreach item=product from=$products}>
                        <td><{include file="db:myservices_oneproduct.tpl" product=$product}></td>
                        <{if $product.count is div by $ProductsPerColumn}>
                    </tr>
                    <tr>
                        <{/if}>
                        <{/foreach}>
                    </tr>
                </table>
            </td>
        <{else}>
            <td>
                <br>
                <h3><{$smarty.const._MYSERVICES_SORRY_NOPROD}></h3>
            </td>
        <{/if}>
    </tr>
</table>
