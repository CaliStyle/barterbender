<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="menu">
    <ul>
        {foreach from=$aMenus key=sPhrase item=sLink name=content}
        <li class="{if count($aMenus) == $phpfox.iteration.content} last{/if}{if $phpfox.iteration.content == 1} first active{/if}"><a href="{$sLink}">{$sPhrase}</a></li>
        {/foreach}
    </ul>
</div>
<div id="js_details_container" class="ynfr-detail-info">
    {if $sType == 'description'}
    <div class="listing_detail">
		<h2 class="ynfr-title-block">
			{phrase var='description'}
		</h2>
        <div class="short_description item_view_content">
            {if !empty($aCampaign.description)}
                {if Phpfox::getParam('core.allow_html')}
                    {$aCampaign.description_parsed|parse}
                {else}
                    {$aCampaign.description|parse}
                {/if}
            {else}
               {$aCampaign.short_description}
            {/if}
        </div>
		<h2 class="ynfr-title-block">
			<span>{phrase var='location_upper'}: <b>{$aCampaign.location_venue}{if $aCampaign.city}, {$aCampaign.city} {/if}{if $aCampaign.country_iso}, {$aCampaign.country_iso|location}{/if} </b></span>
		</h2>
        <iframe width="510" height="430" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="//maps.google.com/maps?key={$googleApiKey}&amp;f=q&amp;source=s_q&amp;geocode=&amp;q={$aCampaign.location_venue}+{$aCampaign.country_iso|location}+{$aCampaign.city}&amp;aq=&amp;sll={$aCampaign.latitude},{$aCampaign.longitude}&amp;sspn=0,0&amp;vpsrc=6&amp;doflg=ptk&amp;ie=UTF8&amp;hq={$aCampaign.location_venue}+{$aCampaign.country_iso|location}+{$aCampaign.city}&amp;ll={$aCampaign.latitude},{$aCampaign.longitude}&amp;spn=0,0&amp;t=m&amp;z=12&amp;output=embed"></iframe>
	</div>
    {elseif $sType == 'donations'}
	<div class="table-responsive">
	<table class="table default_table">
	    {if count($aCampaign.donations) > 0}
		{foreach from=$aCampaign.donations name=iKey item=aDonation}
		<tr class="checkRow">
		    <td class="pet_img_tit">
				{if $aUser = $aDonation}{/if}
                {module name='fundraising.campaign.user-image-entry'}
		    </td>
            <td class="">
                <div>
                    {if $aDonation.is_anonymous }
                        {phrase var='anonymous_donate' amount=$aDonation.amount_text}
                    {else}
                        {if $aDonation.is_guest}
                            {phrase var='user_donate' name=$aDonation.donor_name amount=$aDonation.amount_text}
                        {else}
                            {phrase var='user_donate' name=$aDonation|user amount=$aDonation.amount_text}
                        {/if}
                    {/if}
                    <div class="extra_info">
                        {$aDonation.message}
                    </div>
                </div>
            </td>
		    <td class="ynfr-table-mw-120">{$aDonation.time_stamp|convert_time:'feed.feed_display_time_stamp'}</td>
		</tr>
		{/foreach}
	    {else}
		<tr>
		    <td colspan="2" style="text-align: center; padding: 10px">
			{phrase var='there_are_no_fundraising_donation'}
		    </td>
		</tr>
	    {/if}	    
	</table>
	</div>
    {elseif $sType == 'news'}
	{if $aCampaign.user_id == Phpfox::getUserId()}
	{$sCreateJs}
        {$sCheckFormNewsLink}
	{literal}
	<script type="text/javascript">            
		function valid_form_news()
		{
			if(checkFormNewsLink() && Validation_js_form_news())
			{
				$("#js_form_news").ajaxCall('fundraising.postNews'); 
			}
			return false;
		}
		
		function editNews(id){
			$('#news_headline').val($('#headline_'+id).html());
			$('#news_link').val($('#link_'+id).html());

			var sContent = $('#content_'+id).html().replace(new RegExp('<div class="newline"></div>','g'),'\n');
			$('#news_content').val(sContent);

			$('#news_id').val(id);
			$('#post_news').hide();
			$('#update_news').show();
			$('#js_fundraising_detail').scrollTop(0);
		}
	</script>
	{/literal}
	<div class="info_holder news_detail">
		<div class="news">
			<div class="table form-group">
				<div class="table_left" style="color: #333333;font-size: 14px;font-weight: bold">{phrase var='post_a_news_update'}</div>
				<div class="table_right"></div>
			</div>
			<form id="js_form_news" method="post" action="#" onsubmit="return valid_form_news();" onreset="$('#update_news').hide(); $('#post_news').show(); $('#news_id').val(''); ">
				<input type="hidden" name="val[campaign_id]" value="{$aCampaign.campaign_id}"/>
				<input type="hidden" name="val[news_id]" id="news_id" value=""/>
				<div class="table form-group">
					<div class="table_left">{required} {phrase var='news_headline'}</div>
					<div class="table_right"><input class="form-control" type="text" name="val[news_headline]" id="news_headline"/></div>
				</div>
				<div class="table form-group">
					<div class="table_left"> {phrase var='link'}</div>
					<div class="table_right">
							   <input class="form-control" type="text" name="val[news_link]" id="news_link"/>
							   <div class="help-block">
								  {phrase var='example_http_www_yourwebsite_com'}
							   </div>
							</div>
				</div>
				<div class="table form-group">
					<div class="table_left">{required} {phrase var='content'}</div>
					<div class="table_right"><textarea class="form-control" name="val[news_content]" id="news_content"></textarea></div>
				</div>
				{if Phpfox::getParam('core.display_required')}
				<div class="table_clear">
					{required} {phrase var='required_fields'}
				</div>
				{/if}
				<div class="table_clear">
					<button type="submit" name="val[post_news]" id="post_news" class="btn-sm btn-primary" value="{phrase var='post'}">{phrase var='post'}</button>
					<div id="update_news" style="display: none">
						<button type="submit" name="val[update_news]" class="btn btn-sm btn-primary" value="{phrase var='update'}">{phrase var='update'}</button>
						<button type="reset" class="btn btn-sm btn-default" value="{phrase var='cancel'}" onclick="$('#news_id').val('');">{phrase var='cancel'}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	{/if}
	{if count($aCampaign.news) > 0}
	<div class="fundraising_discuss">
	    {foreach from=$aCampaign.news item=aNews}
	    <div class="discussion_id" id="news_{$aNews.news_id}">
		    <div class="pet_dis_tit" id="headline_{$aNews.news_id}" style="width: 80%">{$aNews.headline}</div>
		    <div class="extra_info">{$aNews.time_stamp|date:'core.global_update_time'}</div>
		    <div class="short_description item_view_content" id="content_{$aNews.news_id}">
			{$aNews.content|parse}
		    </div>
		    <br/>
		    {if !empty($aNews.link)}
		    <div class="short_description">{phrase var='more_at'} <a href="{$aNews.link}" id="link_{$aNews.news_id}" target="_blank">{$aNews.link}</a></div>
		    {/if}
                {if $aCampaign.user_id == Phpfox::getUserId()}
		    <ul class="actions">
			<li><a href="JavaScript:void(0);" onclick="editNews({$aNews.news_id})">{phrase var='edit'}</a> </li>
			<li> / </li>
			<li>
                <a href="JavaScript:void(0);" onclick="$Core.jsConfirm({l}message: '{_p var='are_you_sure_you_want_to_delete_this_news'}'{r}, function(){l} $.ajaxCall('fundraising.deleteNews', 'news_id={$aNews.news_id}'); {r},function(){l}{r}); if( $('#news_id').val() == {$aNews.news_id}) $('#js_form_news')[0].reset(); return false;">{phrase var='delete'}</a>
            </li>
		     </ul>
                 {/if}
	    </div>
	    {/foreach}
	</div>
	{else}
		<span class="ynfr-no-update">{phrase var='there_are_no_news_update'}</span>
	{/if}
    {elseif $sType == 'about'}
	<div class="ynfr-about">
        <table>
            {if !empty($aCampaign.contact_full_name)}<tr><td>{phrase var='full_name'}:</td><td>{$aCampaign.contact_full_name}</td></tr>{/if}
            {if !empty($aCampaign.contact_phone)}<tr><td>{phrase var='phone'}:</td><td>{$aCampaign.contact_phone}</td></tr>{/if}
            {if !empty($aCampaign.contact_email_address)}<tr><td>{phrase var='email'}:</td><td>{$aCampaign.contact_email_address}</td></tr>{/if}
            {if !empty($aCampaign.contact_country_iso)}<tr><td>{phrase var='country'}:</td><td>{$aCampaign.contact_country_iso|location}</td></tr>{/if}
            {if !empty($aCampaign.contact_state)}<tr><td>{phrase var='state'}:</td><td>{$aCampaign.contact_state}</td></tr>{/if}
            {if !empty($aCampaign.contact_city)}<tr><td>{phrase var='city'}:</td><td>{$aCampaign.contact_city}</td></tr>{/if}
            {if !empty($aCampaign.contact_street)}<tr><td>{phrase var='street'}:</td><td>{$aCampaign.contact_street}</td></tr>{/if}
        </table>
        {if !empty($aCampaign.contact_about_me)}
        <h2 class="ynfr-title-block">
            <span>{phrase var='about_us'}</span>
        </h2>
		<div class="item_view_content">
			{$aCampaign.contact_about_me|parse}
		</div>
        {/if}
	</div>
    {/if}
</div>
