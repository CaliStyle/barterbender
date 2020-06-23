<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/19/16
 * Time: 8:39 AM
 */
?>
<div id="menu_seller_my_requests" class="sub_section_menu header_display">
    <ul class="action">
        <li class="{if $sFullControllerName == 'ynsocialstore.statistic'}active{/if}">
            <a href="{url link='ynsocialstore.statistic'}">{_p var='ynsocialstore.statistics'}</a>
        </li>
        <li class="{if $sFullControllerName == 'ynsocialstore.manage-store'}active{/if}">
            <a href="{url link='ynsocialstore.manage-store'}">{_p var='ynsocialstore.my_stores'}</a>
        </li>
        {if !$bDisableVitualMoney}
        <li class="{if $sFullControllerName == 'ecommerce.my-requests'}active{/if}">
            <a href="{url link='ynsocialstore.my-requests'}">{_p var='ecommerce.my_requests'}</a>
        </li>
        {/if}
        <li class="{if in_array($sFullControllerName, array('ecommerce.manage-orders', 'ecommerce.order-detail')) }active{/if}">
            <a href="{url link='ynsocialstore.all-sales'}">{_p var='ynsocialstore.all_sales'}</a>
        </li>
    </ul>
</div>

{literal}
<script type="text/javascript">
    $Behavior.onLoadMenuSeller = function(){
        if($('#page_ynsocialstore_manage-photos').length >0 || $('#page_ynsocialstore_add').length > 0 || $('#page_ynsocialstore_manage-attributes').length >0){
            $('#js_block_border_core_menusub .sub_section_menu ul > li.active').removeClass("active");
            $('#js_block_border_core_menusub .header_display ul > li.active').removeClass("active");
            $('#js_block_border_core_menusub .sub_section_menu ul > li:first').addClass("active");
            $('#js_block_border_core_menusub .header_display ul > li:first').addClass("active");

            $('#menu_seller_my_requests ul > li:eq(1)').addClass('active');
        }
        if($('#page_ynsocialstore_add').length > 0 || $('#page_ynsocialstore_manage-attributes').length >0 || $('#page_ynsocialstore_manage-photos').length >0 || $('#page_ynsocialstore_manage-store').length >0 || $('#page_ynsocialstore_settings').length >0 ||$('#page_ynsocialstore_manage-orders').length >0||$('#page_ecommerce_my-requests').length >0 || $('#page_ecommerce_order-detail').length >0 || $('#page_ecommerce_manage-orders').length >0){
            $('#js_block_border_core_menusub .sub_section_menu ul > li.active').removeClass("active");
            $('#js_block_border_core_menusub .header_display ul > li.active').removeClass("active");
            $("#js_block_border_core_menusub .sub_section_menu ul > li > a:contains('{/literal}{_p var='ynsocialstore.seller_section'}{literal}')").parent('li').addClass("active");
            $("#js_block_border_core_menusub .header_display ul > li > a:contains('{/literal}{_p var='ynsocialstore.seller_section'}{literal}')").parent('li').addClass("active");
        }
    }
</script>
{/literal}
