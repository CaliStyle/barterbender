<div class="auction-app feed {if empty($aAuction.image_path)}no-photo{/if}">
    <div class="auction-media">
        <a class="item-media-bg" href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}"
           style="background-image: url(
           {if isset($aAuction.image_path)}
                {img server_id=$aAuction.server_id path='core.url_pic' file=$aAuction.image_path suffix='_1024' return_url=true}
            {else}
                {$aAuction.default_logo_path}
            {/if}
           )">
        </a>
    </div>
    <div class="auction-inner pl-2 pr-2">
        <a href="{permalink id=$aAuction.product_id module='auction.detail' title=$aAuction.name}" class="auction-title fw-bold">{$aAuction.name|clean}</a>
        <div class="auction-description item_view_content">{$aAuction.description_parsed|stripbb|feed_strip|split:55|max_line}</div>
    </div>
</div>