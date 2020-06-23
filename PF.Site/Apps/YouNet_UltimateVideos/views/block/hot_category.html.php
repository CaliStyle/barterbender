<div class="dont-unbind-children ultimatevideo-category-container {$sCustomContainerClassName}">
    {foreach from=$aCategories item=aCategory}
        <div class="item ultimatevideo-category-item">
            <div class="item-outer">
                <div class="item-media">
                    <a href="{permalink module='ultimatevideo.category' id=$aCategory.category_id title=$aCategory.title}"
                       class="item-media-link">
                        <span class="item-media-src"
                              style="background-image: url({if $aCategory.image_path}{img server_id=$aCategory.image_server_id path='core.url_pic' file=$aCategory.image_path suffix='_500' return_url=true}{else}{param var='core.path_actual'}PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_video.jpg{/if})"></span>
                    </a>
                </div>
                <div class="item-inner">
                    <div class="item-name">{_p var=$aCategory.title}</div>
                </div>
            </div>
        </div>
    {/foreach}
</div>