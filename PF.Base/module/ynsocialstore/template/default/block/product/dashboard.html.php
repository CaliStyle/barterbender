<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/31/16
 * Time: 9:18 AM
 */
?>

<div id="menu_seller_product_dash_board" class="page_section_menu page_section_menu_header">
    <ul class="nav nav-tabs nav-justified">
        <li class="{if $sFullControllerName == 'ynsocialstore.add'}active{/if}">
            <a href="{url link='ynsocialstore.add'}?id={$iProductId}">{_p var='ynsocialstore.edit_information'}</a>
        </li>
        <li class="{if $sFullControllerName == 'ynsocialstore.manage-photos'}active{/if}">
            <a href="{url link='ynsocialstore.manage-photos'}?id={$iProductId}">{_p var='ynsocialstore.manage_photos'}</a>
        </li>
        {if $aPackage.enable_attribute}
        <li class="{if $sFullControllerName == 'ynsocialstore.manage-attributes'}active{/if}">
            <a href="{url link='ynsocialstore.manage-attributes'}?id={$iProductId}">{_p var='ynsocialstore.manage_attributes'}</a>
        </li>
        {/if}
        <li class="{if $sFullControllerName == 'ynsocialstore.product-sales'}active{/if}">
            <a href="{url link='ynsocialstore.product-sales'}?id={$iProductId}">{_p var='ynsocialstore.sales'}</a>
        </li>
<!--         <li class="">
            <a href="{permalink module='ynsocialstore.product' id=$iProductId}">{_p var='ynsocialstore.view_this_product'}</a>
        </li> -->
    </ul>
</div>
