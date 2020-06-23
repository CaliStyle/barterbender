<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/26/16
 * Time: 10:57 AM
 */
?>

<div class="ynstore-product-detail-images" style="visibility: hidden">
    <input type="hidden" id="ynsocialstore_corepath" value="{param var='core.path_file'}">
    <div id="ynstore_status_{$iProductId}" class="ynstore-status-block">
        <div class="ynstore-status ynstatus_{$sStatus}">{_p var='ynsocialstore.'$sStatus}</div>
    </div>
    {if count($aItem) > 0}
        <ul id="ynstore-product-detail-images-big">
            {foreach from=$aItem item=aImage}
            <li class="item easyzoom easyzoom--overlay">
                <a href="{img server_id=$aImage.server_id path='core.url_pic' return_url='true' file=$aImage.image_path}">
                    <img src="{img server_id=$aImage.server_id path='core.url_pic' file=$aImage.image_path return_url='true' suffix=''}" alt="">
                </a>
            </li>
            {/foreach}
        </ul>          

        <ul id="ynstore-product-detail-images-small">
            {foreach from=$aItem item=aImage}
            <li class="item" style="background-image: url({img server_id=$aImage.server_id path='core.url_pic' return_url='true' file=$aImage.image_path suffix='_400'})">
            </li>
            {/foreach}
        </ul>        
    {else}
        <!--   Default image is here     -->
        <img src="{param var='core.path'}module/ynsocialstore/static/image/product_default.jpg">
    {/if}
</div>
