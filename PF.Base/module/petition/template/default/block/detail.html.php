<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if $iPage > 1}
<!-- paging error in fox 4.0.7 so we dont process -->
{else}
<div class="menu">
    <ul>
        {foreach from=$aMenus key=sPhrase item=sLink name=content}
        	<li class="{if count($aMenus) == $phpfox.iteration.content} last{/if}{if $phpfox.iteration.content == 1} first active{/if}"><a href="{$sLink}">{$sPhrase}</a></li>
        {/foreach}
    </ul>
</div>
<div id="js_details_container">

    {if $sType == 'description'}
		{if phpfox::isMobile()}
	<h3>{phrase var='petition.description'}</h3>
	{/if}
    <div class="listing_detail">
        <div class="short_description item_view_content">
            {if !empty($aPetition.description)}
               {$aPetition.description|parse}
            {else}
               {$aPetition.short_description}
            {/if}
        </div>
    </div>
    {elseif $sType == 'letter'}
{if phpfox::isMobile()}
	<h3>{phrase var='petition.petition_letter'}</h3>
	{/if}
	<div class="pet_let_tit">{$aPetition.letter_subject}</div>
	<div class="pet_let_cont item_view_content">
	    {$aPetition.letter|parse}
	</div>
    {elseif $sType == 'signatures'}
{if phpfox::isMobile()}
	<h3>{phrase var='petition.signatures'}</h3>
	{/if}
	<div class="pet_sign">
		 
	<table id="tblsignatures">
	    <tr class="pet_sign_tit"><td>{phrase var='petition.members'}</td><td>{phrase var='petition.why_they_are_signing'}</td></tr>
	    {if count($aPetition.signatures) > 0}
		{foreach from=$aPetition.signatures name=iKey item=aSignature}
		<tr class="checkRow{*{if is_int($iKey/2)}*} tr {*{else}{/if}*}">
		    <td class="pet_img_tit">
			 {img user=$aSignature suffix='_50_square' max_width=50 max_height=50}
			 <div class="row_title_info">
			    {$aSignature|user}
			    <div class="extra_info">{phrase var='petition.on'} {$aSignature.time_stamp|date:'petition.petition_time_stamp'}
				</br>   {phrase var='petition.at'} {$aSignature.location|clean'}
			    </div>
			</div>
		    </td>
		    <td class="pet_res_tit">{$aSignature.signature}</td>
		</tr>
		{/foreach}
		
	    {else}
		<tr>
		    <td colspan="2" style="text-align: center; padding: 10px">
			{phrase var='petition.there_are_no_petition_signatures'}
		    </td>
		</tr>
	    {/if}
	</table>
	{if count($aPetition.signatures) > 0}
		<div class="clear"></div>
		<div id="ynPeti_sign">
		 
		</div>
	{/if}
	</div>
    {elseif $sType == 'news'}
{if phpfox::isMobile()}
	<h3>{phrase var='petition.news'}</h3>
		{/if}
	{if $aPetition.user_id == Phpfox::getUserId()}
	{$sCreateJs}
      {$sCheckFormNewsLink}
	{literal}
	<script type="text/javascript">
		function valid_form_news()
		{
			$('#js_form_news_msg').html("");
			if(checkFormNewsLink() && Validation_js_form_news())
			{
				$("#js_form_news").ajaxCall('petition.postNews');
			}
			return false;
		}

		function editNews(id){
			$('#news_headline').val($('#headline_'+id).html());
			$('#news_link').val($('#link_'+id).html());
			$('#news_content').val($('#content_'+id).html());
			$('#news_id').val(id);
			$('#post_news').hide();
			$('#update_news').show();
			$('#js_petition_detail').scrollTop(0);
		}
	</script>
	{/literal}
	<div class="info_holder news_detail">
		<div class="news">
			<h3>{phrase var='petition.post_a_news_update'}</h3>
			<form id="js_form_news" method="post" action="#" onsubmit="return valid_form_news();" onreset="$('#update_news').hide(); $('#post_news').show(); $('#news_id').val(''); ">
			<input type="hidden" name="val[petition_id]" value="{$aPetition.petition_id}"/>
			<input type="hidden" name="val[news_id]" id="news_id" value=""/>
			<div class="table form-group">
				<div class="table_left">{required} {phrase var='petition.news_headline'}</div>
				<div class="table_right"><input class="form-control" type="text" name="val[news_headline]" id="news_headline"/></div>
			</div>
			<div class="table form-group">
				<div class="table_left"> {phrase var='petition.link'}</div>
				<div class="table_right">
                           <input type="text" class="form-control" name="val[news_link]" id="news_link" />
                           <div class="extra_info">
                              {phrase var='petition.example_http_www_yourwebsite_com'}
                           </div>
                        </div>
			</div>
			<div class="table form-group">
				<div class="table_left">{required} {phrase var='petition.content'}</div>
				<div class="table_right"><textarea class="form-control" name="val[news_content]" id="news_content" style="height: 80px;"></textarea></div>
			</div>
			{if Phpfox::getParam('core.display_required')}
					<div class="table_clear">{required} {phrase var='petition.required_fields'}</div>
				{/if}
			<div class="table_clear">
				<input type="submit" name="val[post_news]" id="post_news" class="btn btn-primary btn-sm" value="{phrase var='petition.post'}"/>
				<div id="update_news" style="display: none">
					<input type="submit" name="val[update_news]" class="btn btn-primary btn-sm" value="{phrase var='petition.update'}"/>
					<input type="reset" class="btn btn-default btn-sm" value="{phrase var='petition.cancel'}" onclick="$('#news_id').val('');"/>
				</div>
			</div>
			</form>
		</div>
	</div>
	{/if}
	{if count($aPetition.news) > 0}
	<div class="petition_discuss">
	    {foreach from=$aPetition.news item=aNews}
	    <div class="discussion_id" id="news_{$aNews.news_id}">
		    <div class="pet_dis_tit" id="headline_{$aNews.news_id}">{$aNews.headline}</div>
		    <div class="extra_info">{$aNews.time_stamp|date:'petition.petition_time_stamp'}</div>
		    <div class="short_description item_view_content" id="content_{$aNews.news_id}">
			{$aNews.content|parse}
		    </div>
		    {if !empty($aNews.link)}
		    <div class="short_description item_view_content">{phrase var='petition.more_at'} <a href="{$aNews.link}" id="link_{$aNews.news_id}" target="_blank">{$aNews.link}</a></div>
		    {/if}
                {if $aPetition.user_id == Phpfox::getUserId()}
		    <ul class="actions">
			<li><a href="JavaScript:void(0);" onclick="editNews({$aNews.news_id})">{phrase var='petition.edit'}</a> </li>
			<li> / </li>
			<li><a href="JavaScript:void(0);" onclick="if(confirm('{phrase var='petition.are_you_sure_you_want_to_delete_this_news' phpfox_squote=true}')) $.ajaxCall('petition.deleteNews', 'news_id={$aNews.news_id}'); if( $('#news_id').val() == {$aNews.news_id}) $('#js_form_news')[0].reset(); return false;">{phrase var='petition.delete'}</a></li>
		     </ul>
                 {/if}
	    </div>
	    {/foreach}
	    <div class="clear"></div>
	    
	</div>
	{else}
		{phrase var='petition.there_are_no_news_update'}
	{/if}
    {/if}
</div>
{/if}
{literal}
<script type="text/javascript">
	$Behavior.addclassTabs = function(){
		$('#js_block_border_petition_detail .menu').addClass('page_section_menu');
		$('#js_block_border_petition_detail .menu ul').addClass('nav nav-tabs nav-justified');
	}
</script>
{/literal}