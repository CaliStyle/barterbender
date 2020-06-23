<div class="yndirectory-detail-overview">
	{if $aBusiness.short_description != ''}
	
	<div class="yndirectory-detail-overview-item wrap-longtext">
		{$aBusiness.short_description|parse}	
	</div>
	{/if}
	<div class="yndirectory-detail-overview-item">
		<h5><span><i class="ico ico-user2-two-o"></i>{phrase var='business_sizes'}<span>{$aBusiness.size}</span></span></h5>
	</div>

	{if !$aBusiness.disable_visitinghourtimezone}
	<div class="yndirectory-detail-overview-item">
		<h5 class="yndirectory-line"><span><i class="ico ico-clock-o"></i>{phrase var='operating_hours'}</span></h5>
		<div class="yndirectory-overview-item-content">
		<div class="yndirectory-detail-timezone"><span class="time-title">{phrase var='timezone'}:</span>{$aBusiness.time_zone} </div>

		{if count($aBusiness.vistinghours)}
		<div class="yndirectory-detail-hour-list">
			{foreach from=$aBusiness.vistinghours key=iVistingHour item=aVistingHour}
			<div class="yndirectory-detail-hour-item">
				<div class="item-day">{$aVistingHour.vistinghour_dayofweek_phrase}</div>
                {if $aVistingHour.vistinghour_starttime != 'Closed' && $aVistingHour.vistinghour_endtime != 'Closed'}
                {if $aVistingHour.vistinghour_starttime || $aVistingHour.vistinghour_endtime}
                    <div class="item-hour">{$aVistingHour.vistinghour_starttime} - {$aVistingHour.vistinghour_endtime}</div>
                {/if}
                {else}
                <div>Closed</div>
                {/if}
			</div>
			{/foreach}
		</div>
		{/if}
		</div>
	</div>
	{/if}


	{if $aBusiness.founder != ''}
	<div class="yndirectory-detail-overview-item">
		<h5 class="yndirectory-line"><span><i class="ico ico-user2-edit-o"></i>{phrase var='founder'}</span></h5>
		<div class="yndirectory-overview-item-content">
			{$aBusiness.founder}	
		</div>
	</div>
	{/if}

	<div class="yndirectory-detail-overview-item">
		<h5 class="yndirectory-line"><span><i class="ico ico-envelope-o"></i>{phrase var='contact_information'}</span></h5>
		<div class="yndirectory-overview-item-content">
			{if count($aBusiness.phones)}
			<div class="yndirectory-detail-overview-contact-item">
				<div class="item-title">{phrase var='phone'}:</div>
				<div class="item-info">
					{foreach from=$aBusiness.phones key=iPhone item=aPhone}
						<div>{$aPhone.phone_number}</div>
					{/foreach}
				</div>
			</div>
			{/if}

			{if count($aBusiness.faxs)}
			<div class="yndirectory-detail-overview-contact-item">
				<div class="item-title">{phrase var='fax'}:</div>
				<div class="item-info">
					{foreach from=$aBusiness.faxs key=iFax item=aFax}
					<div>{$aFax.fax_number}</div>
					{/foreach}
				</div>
			</div>
			{/if}			

			{if $aBusiness.email != ''}
			<div class="yndirectory-detail-overview-contact-item">
				<div class="item-title">{phrase var='email'}:</div>
				<div class="item-info"><a href="mailto:{$aBusiness.email}">{$aBusiness.email}</a></div>
			</div>
			{/if}			

			{if count($aBusiness.websites)}
			<div class="yndirectory-detail-overview-contact-item website">
				<div class="item-title">{phrase var='website'}:</div>
				<div class="item-info">
					{foreach from=$aBusiness.websites key=iWebsite item=aWebsite}
						<div><a href="{$aWebsite.link}" target="_blank">{$aWebsite.website_text}</a></div>
					{/foreach}
				</div>
			</div>
			{/if}
		</div>
	</div>
	{if isset($isPrintPage) && $isPrintPage == 0 && !empty($aBusiness.location_address)}
        <div class="yndirectory-detail-overview-item">
            <h5 class="yndirectory-line"><span><i class="ico ico-map-o"></i>{phrase var='locations'}</span></h5>
            <div class="yndirectory-overview-item-content">
                <div id="yndirectory_detail_mapview" style="width: 100%;height:200px;" ></div>
            </div>
	    </div>
	{elseif !empty($aBusiness.location_address)}
        <div class="yndirectory-detail-overview-item">
            <h5 class="yndirectory-line"><span><i class="ico ico-map-o"></i>{phrase var='locations'}</span></h5>
            <div class="yndirectory-overview-item-content">
                <div class="block" id="js_block_border_directory_detailcover">
                    <div class="yndirectory-detailcover-maps">
                        <div id="yndirectory_cover_maps"></div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
	<div class="yndirectory-detail-overview-item">
        {if count($aBusiness.additioninfo) || count($aCustomFields)}
            <h5 class="yndirectory-line"><span><i class="ico ico-file-text-o"></i>{phrase var='additional_information'}</span></h5>
        {/if}
		<div class="yndirectory-overview-item-content">
            {if count($aBusiness.additioninfo)}
                {foreach from=$aBusiness.additioninfo key=iAdditionalInfo item=aAdditionalInfo }
                <div class="yndirectory-detail-overview-additional-item">
                    <div>
                        {$aAdditionalInfo.usercustomfield_title}:
                    </div>
                    <div>
                        {$aAdditionalInfo.usercustomfield_content}
                    </div>
                </div>
                {/foreach}
            {/if}
            {template file='directory.block.custom.view'}
		</div>
	</div>

	{if $aBusiness.description != ''}
	<div class="yndirectory-detail-overview-item">
		<h5 class="yndirectory-line"><span><i class="fa fa-align-justify"></i>{phrase var='description'}</span></h5>
		<div class="yndirectory-overview-item-content">
			<div class="yndirectory-description item_view_content">
				{$aBusiness.description|parse}	
			</div>
		</div>
	</div>
	{/if}

    {if isset($sTextCategories) && null != $sTextCategories && count($sTextCategories) > 0}
    <div class="ync-item-info-group">
        <div class="ync-item-info">
            <div class="ync-category">
                <span class="ync-item-label">{_p var='directory.category'}:</span>
                <div class="ync-item-content">{$sTextCategories|category_display}</div>
            </div>
        </div>
    </div>
    {/if}

    <div class="ync-addthis">{addthis url=$aBusiness.bookmark_url title=$aBusiness.name}</div>
	{if count($aSuggestedBusinesses)}
	<div class="yndirectory-suggested-block ync-block">
		<h3>{phrase var='suggested_businesses'}</h3>
		<div class="item-container yndirectory-content-item-list ync-listing-container col-3" data-mode-view="grid" data-mode-view-default="grid" style="">
        {foreach from=$aSuggestedBusinesses item=aBusiness name=business}
        <div class="ync-item">
        	<div class="business-item">
		        <div class="business-item-outer">
		            <div class="business-item-images-wrapper">
		                <a class="business-item-images" href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" title="{$aBusiness.name|clean}">
		                    <span class="yndirectory-photo-span">
                                {if $aBusiness.logo_path}
                                    {img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_400'}
                                {else}
                                    <img src="{$aBusiness.default_logo_path}" alt="">
                                {/if}
                            </span>
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
		                    <span class="business-item-location">
		                        <span class="short-location">
		                            {if !empty($aBusiness.country_iso)}
		                                {$aBusiness.country_iso|location}
		                            {/if}
		                        </span>
		                    </span>
		                   
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
		                </div>
		            </div>

		        </div>

		    </div>
		</div>
        {/foreach}
		</div>
	</div>
	{/if}
</div>

{literal}
<script type="text/javascript">
;
    $Behavior.loadOverDetailBusiness = function(){
        $( document ).ready(function() {
            $('.yndirectory-detail-overview-item .yndirectory-line').click(function() {
                $(this).parent().toggleClass('yndirectory-collapse');
            });
        });
    }
</script>
{/literal}
