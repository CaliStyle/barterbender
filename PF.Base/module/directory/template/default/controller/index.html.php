<?php 
/**
 *
 * @copyright		[YouNetCo]
 * @author  		TriLM
 */

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $iPage == 0}
<div id="yndirectory_index">
	<div class="yndirectory-hiddenblock">
		<input type="hidden" value="index" id="yndirectory_pagename" name="yndirectory_pagename">
	</div>
	<div>
		<input type="hidden" id='yndirectory_condition' name="yndirectory_condition" value="{if isset($sCondition)}{$sCondition}{/if}">
		<input type="hidden" id="yndirectory_has_init_modeview" value="0" />
	</div>
	<div>
	{/if}
		{if $bIsHomepage}
		{else}

        {if !count($aItems)}
            {if $iPage <= 1}
            <div class="yndirectory-index-container">
                <div class="ync-block">
                    <div class="content">
                        <div class="help-block">
                            {_p var='no_businesses_found'}
                        </div>
                    </div>
                </div>
            </div>
            {/if}
        {else}
            {if !PHPFOX_IS_AJAX}
            <div class="yndirectory-index-container p-profile-listing-container-theme">
                <div class="ync-block">
                    <div class="content">
                        <div class="ync-mode-view-container yndirectory-listing-js">
                            <span class="ync-mode-view-btn casual" data-mode="casual" title="{_p var='casual_view'}">
                                <i class="ico ico-casual icon-normal" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_casual.svg)"></i>
                                <i class="ico ico-casual icon-hover" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_casual_dark.svg)"></i>
                            </span>
                            <span class="ync-mode-view-btn grid" data-mode="grid" title="{_p var='grid_view'}"><i class="ico ico-th"></i></span>
                            <span class="ync-mode-view-btn list" data-mode="list" title="{_p var='list_view'}">
                                <i class="ico ico-list icon-normal" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_list.svg)"></i>
                                <i class="ico ico-list icon-hover" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_list_dark.svg)"></i>
                            </span>
                        </div>
                        <div class="item-container yndirectory-content-item-list ync-listing-container ync-view-modes-js" data-mode-view="" data-mode-view-default="grid">
            {/if}
                            {foreach from=$aItems item=aBusiness name=business}
                            <article class="ync-item">{template file='directory.block.listing-business-item'}</article>
                            {/foreach}
                            {pager}
            {if !PHPFOX_IS_AJAX}
                        </div>
                    </div>
                </div>
            </div>
            {/if}

            {if $sView == 'mybusinesses'
                }
                {moderation}
            {/if}

            <div class="clear"></div>
        {/if}
	{/if}
{if $iPage == 0}	
	</div>
</div>
{/if}
{if $sView == 'mybusinesses'}
{literal}
<script>
	$Behavior.loadMyBusiness = function(){
		$('body').addClass('yndirectory-mybusiness-compare');
	}
</script>
{/literal}
{else $sView != 'mybusinesses'}
{literal}
<script>
	$Behavior.loadMyBusiness = function(){
		$('body').removeClass('yndirectory-mybusiness-compare');
	}
</script>
{/literal}

{/if}
{literal}
<script>
	window.initialize = function initialize() {}
</script>
{/literal}

{literal}
<script>
    $Behavior.bCheckModeViewAll = function(){
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

<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$apiKey}&v=3.exp&libraries=places&callback=initialize"></script>
