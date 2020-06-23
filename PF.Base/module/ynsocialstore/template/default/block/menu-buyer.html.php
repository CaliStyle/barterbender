<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/19/16
 * Time: 8:39 AM
 */
?>

<div id="ynstore_menu_buyer" class="sub_section_menu header_display">
    <ul class="action">
        <li class="{if $sFullControllerName == 'ynsocialstore.my-cart'}active{/if}">
            <a href="{url link='ynsocialstore.my-cart'}">{_p var='ynsocialstore.my_cart'}</a>
        </li>
        <li class="{if $sFullControllerName == 'ynsocialstore.my-wishlist'}active{/if}">
            <a href="{url link='ynsocialstore.my-wishlist'}">{_p var='ynsocialstore.my_wishlist'}</a>
        </li>
        <li class="{if in_array($sFullControllerName, array('ecommerce.my-orders', 'ecommerce.order-detail'))}active{/if}">
            <a href="{url link='ynsocialstore.my-orders'}">{_p var='ynsocialstore.my_orders'}</a>
        </li>
        <li class="{if $sFullControllerName == 'ynsocialstore.checkout'}active{/if}">
            <a href="{url link='ynsocialstore.checkout'}">{_p var='ecommerce.checkout'}</a>
        </li>
    </ul>
</div>

{literal}
<script type="text/javascript">
    $Behavior.onLoadMenuBuyer = function(){
        if($('#page_ynsocialstore_my-wishlist').length > 0 || $('#page_ecommerce_my-orders').length > 0 || $('#page_ecommerce_order-detail').length > 0 || $('#page_ynsocialstore_checkout').length > 0){
            $('#js_block_border_core_menusub .sub_section_menu ul > li.active').removeClass("active");
            $('#js_block_border_core_menusub .header_display ul > li.active').removeClass("active");
            $("#js_block_border_core_menusub .sub_section_menu ul > li > a:contains('{/literal}{_p var='ynsocialstore.buyer_section'}{literal}')").parent('li').addClass("active");
            $("#js_block_border_core_menusub .header_display ul > li > a:contains('{/literal}{_p var='ynsocialstore.buyer_section'}{literal}')").parent('li').addClass("active");
        }
    }
</script>
{/literal}
