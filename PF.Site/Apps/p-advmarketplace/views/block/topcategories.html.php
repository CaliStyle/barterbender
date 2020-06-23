{if !$bIsSideLocation}
<div class="p-advmarketplace-category-container p-listing-container" >
    {foreach from=$aCategories item=aCategory}
    <div class="p-item p-advmarketplace-category-item">
        <div class="item-outer">
            <div class="item-outer-custom">
                <a href="{permalink module='advancedmarketplace.search.category' id=$aCategory.category_id title=$aCategory.name}" class="p-advmarketplace-category-item-bg-link p-hidden-side-block"></a>
                <div class="p-item-media-wrapper p-advmarketplace-category-item-media p-hidden-side-block">
                    <div class="item-media-link">
                        <span class="item-media-src" style="background-image: url(
                        {if $aCategory.server_id == '-1'}
                            {img path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/'.$aCategory.image_path return_url=true}
                        {else}
                            {if $aCategory.image_path != NULL}
                                {img server_id=$aCategory.server_id path='advancedmarketplace.url_pic' file=$aCategory.image_path suffix='_120_square' return_url=true}
                            {else}
                                {img path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/default_category.png' return_url=true}
                            {/if}
                        {/if}
                        )"></span>
                    </div>
                </div>
                <div class="item-inner">
                    <div class="item-name p-item-title">
                        <a href="{permalink module='advancedmarketplace.search.category' id=$aCategory.category_id title=$aCategory.name}">
                            {_p var=$aCategory.name}
                        </a>
                    </div>
                    <div class="item-number-post p-item-minor-info">
                        {$aCategory.total_posts} <span class="p-text-lowercase">{$aCategory.total_posts|ync_n:'listing':'listings'}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/foreach}
</div>
{else}
<div class="p-advmarketplace-category-container p-listing-container" data-mode-view="list">
    {foreach from=$aCategories item=aCategory}
    <div class="p-item p-advmarketplace-category-item">
        <div class="item-outer">
            <div class="item-outer-custom">
                <a href="{permalink module='advancedmarketplace.search.category' id=$aCategory.category_id title=$aCategory.name}" class="p-advmarketplace-category-item-bg-link p-hidden-side-block"></a>
                <div class="p-item-media-wrapper p-advmarketplace-category-item-media p-hidden-side-block">
                    <div class="item-media-link">
                        <span class="item-media-src" style="background-image: url(
                        {if $aCategory.image_path != NULL}
                        {img server_id=$aCategory.server_id path='advancedmarketplace.url_pic' file=$aCategory.image_path suffix='_120_square' return_url=true}
                        {else}
                            {img path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/default_category.png' return_url=true}
                        {/if}
                        )"></span>
                    </div>
                </div>
                <div class="item-inner">
                    <div class="item-name p-item-title">
                        <a href="{permalink module='advancedmarketplace.search.category' id=$aCategory.category_id title=$aCategory.name}">
                            {_p var=$aCategory.name}
                        </a>
                    </div>
                    <div class="item-number-post p-item-minor-info">
                        {$aCategory.total_posts} <span class="p-text-lowercase">{$aCategory.total_posts|ync_n:'listing':'listings'}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/foreach}
</div>
{/if}
