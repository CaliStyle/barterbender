
<div id="yndirectory_menu">
    {if isset($viewType)}
    <input type="hidden" value="{$viewType}" id="yndirectory_menu_viewtype" name="yndirectory_menu_viewtype" >
    {/if}
    <input type="hidden" value="{$menu_display_page}" id="yndirectory_menu_display_page" name="yndirectory_menu_display_page" >
</div>

{if count($aBusinessHomepage)>0}
<div class="ync-mode-view-container yndirectory-block-homepage-js">
    <span class="ync-mode-view-btn casual" data-mode="casual" title="{_p var='casual_view'}">
        <i class="ico ico-casual icon-normal" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_casual.svg)"></i>
        <i class="ico ico-casual icon-hover" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_casual_dark.svg)"></i>
    </span>
    <span class="ync-mode-view-btn grid" data-mode="grid" title="{_p var='grid_view'}"><i class="ico ico-th"></i></span>
    <span class="ync-mode-view-btn list" data-mode="list" title="{_p var='list_view'}">
        <i class="ico ico-list icon-normal" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_list.svg)"></i>
        <i class="ico ico-list icon-hover" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_list_dark.svg)"></i>
    </span>
    <span class="ync-mode-view-btn map" data-mode="map" data-callback-js="yndirectory.showMapView();" title="{_p var='map_view'}"><i class="ico ico-map"></i></span>
</div>
<div class="item-container yndirectory-content-item-list ync-listing-container ync-view-modes-js" data-mode-view="" data-mode-view-default="grid">
    {foreach from=$aBusinessHomepage item=aBusiness name=business}
    <article class="ync-item">{template file='directory.block.listing-business-item'}</article>
    {/foreach}
    <div id="yndirectory_mapview" class="yndirectory-map" style="width: 100%;height:530px"></div>
</div>

{else}
<p class="help-block">{_p var='no_businesses_found'}</p>
{/if}

{literal}
<script>
    $Behavior.bCheckModeView = function(){
        var ync_viewmode_data = $('.ync-mode-view-btn.casual');

        if (ync_viewmode_data.hasClass('active')) {
            if ($('.yndirectory-content-item-list.ync-listing-container').hasClass('col-3')){
                $('.yndirectory-content-item-list.ync-listing-container').removeClass('col-3');
            }
            $('.yndirectory-content-item-list.ync-listing-container').addClass('col-2');
            $('.yndirectory-content-item-list.ync-listing-container').masonry();
        } else {
            $('.yndirectory-content-item-list.ync-listing-container').removeClass('col-2').addClass('col-3');
        }

        $(' .ync-mode-view-btn').on('click', function () {

            var ync_viewmode_data = $(this).data('mode');

            if (ync_viewmode_data === 'casual') {
                if ($('.yndirectory-content-item-list.ync-listing-container').hasClass('col-3')){
                    $('.yndirectory-content-item-list.ync-listing-container').removeClass('col-3');
                }
                $('.yndirectory-content-item-list.ync-listing-container').addClass('col-2');
                $('.yndirectory-content-item-list.ync-listing-container').masonry();
            } else {
                $('.yndirectory-content-item-list.ync-listing-container').removeClass('col-2').addClass('col-3');
            }
        });
    }
</script>
{/literal}



