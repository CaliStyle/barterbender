<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="business-item image_hover_holder " id ="js_business_entry{$aBusiness.business_id}">

    {if ($sView == 'mybusinesses')}
    <div class="moderation_row">
        <label class="item-checkbox">
            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aBusiness.business_id}" id="check{$aBusiness.business_id}" />
            <i class="ico ico-square-o"></i>
        </label>
    </div>
    {/if}
    <div class="business-cover-bg only-casualview" style="display: none;">
        <span class="business-coverbg-span" style="background-image: url(
            {if $aBusiness.default_cover}
                {$aBusiness.cover_photo}
            {else}
                {img return_url=true server_id=$aBusiness.cover_photo_server_id path='core.url_pic' file=$aBusiness.cover_photo suffix=''}
            {/if}
        );"></span>
        <div class="business-icon-sticky">
            {if isset($aBusiness.featured) && $aBusiness.featured }
            <div class="sticky-label-icon sticky-featured-icon">
                <span class="flag-style-arrow"></span>
                <i class="ico ico-diamond"></i>
            </div>
            {/if}
            {if $sView == "mybusinesses" }
                {if $aBusiness.business_status == 3}
                <div class="sticky-label-icon sticky-pending-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-clock-o"></i>
                </div>
                {/if}
            {/if}
        </div>
        {if isset($sView) && $sView == "myfollowingbusinesses"}
        <div class="business-item-unfollow">
            <a class="btn btn-xs btn-default btn-icon" href="#" title="Unfollow" onclick="$.ajaxCall('directory.deleteFollow', 'item_id={$aBusiness.business_id}'); return false;"><span class="ico ico-check"></span><span class="item-text">{phrase var='following'}</span></a>
        </div>
        {/if}

        {if isset($sView) && $sView == "myfavoritebusinesses"}
        <div class="business-item-unfavorite">
            <a class="btn btn-xs btn-default btn-icon" href="#" title="Unfavorite" onclick="$.ajaxCall('directory.deleteFavorite', 'item_id={$aBusiness.business_id}'); return false;"><span class="ico ico-check"></span><span class="item-text">{phrase var='favorite'}</span></a>
        </div>
        {/if}
    </div>
    <div class="business-item-outer">
        <div class="business-item-images-wrapper">
            <a class="business-item-images" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" title="{$aBusiness.name|clean}">
                {if isset($aBusiness.logo_path)}
                    {img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_400'}
                {else}
                    <img title="{$aBusiness.name}" src="{$aBusiness.default_logo_path}"/>
                {/if}
            </a>

            <div class="business-icon-sticky">
                {if isset($aBusiness.featured) && $aBusiness.featured }
                <div class="sticky-label-icon sticky-featured-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-diamond"></i>
                </div>
                {/if}
                {if $sView == "mybusinesses" }
                    {if $aBusiness.business_status == 3}
                    <div class="sticky-label-icon sticky-pending-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-clock-o"></i>
                    </div>
                    {/if}
                {/if}
            </div>                    
            

            {if isset($sView) && $sView == "myfollowingbusinesses"}
            <div class="business-item-unfollow">
                <a class="btn btn-xs btn-default btn-icon" href="#" title="Unfollow" onclick="$.ajaxCall('directory.deleteFollow', 'item_id={$aBusiness.business_id}'); return false;"><span class="ico ico-check"></span><span class="item-text">{phrase var='following'}</span></a>
            </div>
            {/if}

            {if isset($sView) && $sView == "myfavoritebusinesses"}
            <div class="business-item-unfavorite">
                <a class="btn btn-xs btn-default btn-icon" href="#" title="Unfavorite" onclick="$.ajaxCall('directory.deleteFavorite', 'item_id={$aBusiness.business_id}'); return false;"><span class="ico ico-check"></span><span class="item-text">{phrase var='favorite'}</span></a>
            </div>
            {/if}
        </div>
        
    
        <div class="business-item-inner">
            {if ($sView == "mybusinesses") || ($bIsProfile && Phpfox::getUserId() == $aBusiness.user_id) }
            <div class="item-option yndirectory-button-option">
                <div class="dropdown">
                        <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
                            <i class="ico ico-gear-o"></i>
                        </span>
                    <ul class="dropdown-menu dropdown-menu-right" style="line-height: 18px">
                        {template file='directory.block.link'}
                    </ul>
                </div>
            </div>
            {/if}
            <div class="item-title">
                <a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" id="js_business_edit_inner_title{$aBusiness.business_id}" class="link ajax_link yndirectory-text-overflow">{$aBusiness.name}</a>
            </div>
            <div class="business-item-title-info">
                <span class="business-item-category">
                    {if Phpfox::isPhrase($this->_aVars['aBusiness']['category_title'])}
                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aBusiness']['category_title']) ?>
                    {else}
                        {assign var='value_name' value=$aBusiness.category_title|convert}
                    {/if}
                    <a href="{permalink module='directory.category' id=$aBusiness.category_id title=$value_name}">{$value_name}</a>
                </span>
                {if !empty($aBusiness.country_iso) && (!empty($aBusiness.country_iso) || !empty($aBusiness.location_title) || !empty($aBusiness.location_address))}
                <span class="business-item-location">
                    {if !empty($aBusiness.country_iso)}
                    <span class="short-location">
                        {$aBusiness.country_iso|location}
                    </span>
                    {/if}
                    <span class="full-location">
                        <span class="location-text"> {phrase var='location'}:</span>
                        {if !empty($aBusiness.country_iso)}
                            {$aBusiness.country_iso|location},
                        {/if}
                        {if !empty($aBusiness.location_title)}
                            {$aBusiness.location_title},
                        {/if}
                        {if !empty($aBusiness.location_address)}
                            {$aBusiness.location_address}
                        {/if}
                    </span>
                </span>
                {/if}
            </div>
            <div class="business-item-short-description item_view_content">{$aBusiness.short_description}</div>
             {if isset($aBusiness.phone_number) }
            <div class="business-info-contact"><i class="ico ico-phone-o"></i><span> {$aBusiness.phone_number}</span></div>
            {/if}
            <div class="business-rating-compare-group">
                <div class="business-item-rating">
                    {if !($aBusiness.total_rating == 0) }
                    <div class="business-rating-star">
                        {$aBusiness.total_score_text}  
                        {if $aBusiness.bCanRateBusiness}
                            <a class="item-write-review-mini" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews"><i class="ico ico-textedit"></i></a>
                        {/if}
                    </div>
                    {/if}
                    {if $aBusiness.bCanRateBusiness}
                        <div class="business-rating-action">
                        {if $aBusiness.total_rating == 0}
                            <a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews"><i class="ico ico-textedit"></i><span>{phrase var='no_reviews_be_the_first'}</span></a>
                        {else}
                             <a class="item-write-review" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}reviews"><i class="ico ico-textedit"></i><span>{phrase var='write_a_review'}</span></a>
                        {/if}
                        </div>
                    {/if}
                </div>
                {if defined('PHPFOX_IS_PAGES_VIEW') || Phpfox::getUserBy('profile_page_id') > 0 || (isset($aProfileUser) && $bIsProfile)}
                {else}
                <div class="business-item-compare">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox"  data-compareitembusinessid="{$aBusiness.business_id}" data-compareitemname="{$aBusiness.name}" data-compareitemlink="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}"
                                   data-compareitemlogopath="{if isset($aBusiness.logo_path)}{img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_100' return_url=true}{else}
                                    {img server_id=$aBusiness.server_id path='' file=$aBusiness.default_logo_path suffix='' return_url=true}{/if}" onclick="yndirectory.clickCompareCheckbox(this);" class="yndirectory-compare-checkbox">  {phrase var='compare'}
                        </label>
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>
    <div class=" business-casual-info only-casualview" style="display: none;">
        <div class="business-item-title-info">
            <span class="business-item-category">
                {if Phpfox::isPhrase($this->_aVars['aBusiness']['category_title'])}
                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aBusiness']['category_title']) ?>
                {else}
                    {assign var='value_name' value=$aBusiness.category_title|convert}
                {/if}
                <a href="{permalink module='directory.category' id=$aBusiness.category_id title=$value_name}">{$value_name}</a>
            </span>
            {if !empty($aBusiness.country_iso)}
            <span class="business-item-location">
                <span class="short-location">
                    {if !empty($aBusiness.country_iso)}
                        {$aBusiness.country_iso|location}
                    {/if}
                </span>
            </span>
            {/if}
        </div>
        <div class="business-item-short-description item_view_content">{$aBusiness.short_description}</div>
    </div>
</div>
