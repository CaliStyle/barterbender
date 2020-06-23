<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/1/16
 * Time: 09:19
 */
?>
<div class="ynstore-product-overview">
    <div class="ynstore-product-overview-item">
        {if count($aCustomFields)}
        <div class="ynstore-title">
            {_p var='ynsocialstore.specifications'}
        </div>
        <div class="ynstore-content">
            {template file='ynsocialstore.block.custom.view'}
        </div>
        {/if}
    </div>
    
    <div class="ynstore-product-overview-item">
        <div class="ynstore-title">
            {_p var='ynsocialstore.information'}
        </div>
        <div class="ynstore-content">
            <div class="ynstore-detail-overview-item">
                <div class="ynstore-description item_view_content">
                    {if Phpfox::getParam('core.allow_html')}
                        {$aProduct.description_parsed|parse}
                    {else}
                        {$aProduct.description|parse}
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
