<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1544 2010-04-07 13:20:17Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<script type="text/javascript">
	function viewTutorial(ele)
	{
		if(ele)
		{
			if($(ele).html() == oTranslations['socialpublishers.view'])
			{
				$('#'+ele.rel).slideDown();
				$(ele).html(oTranslations['socialpublishers.hide']);
			}
			else
			{
				$('#'+ele.rel).slideUp();
				$(ele).html(oTranslations['socialpublishers.view']);
			}
				
		}
		
	}
</script>
<style type="text/css">
	.tip{
		margin-top: 0px;
	}
	div.tip_tutorial
	{
		padding-bottom:4px;
		border-bottom: 1px solid #CFCFCF;
	}
	div.tip_tutorial ul 
	{
    	counter-reset: li;
	}
	div.tip_tutorial ul li 
	{
    	list-style: decimal-leading-zero outside none;
	    margin: 0 0 0 26px;
	    padding: 4px 0;
	    position: relative;
	}
</style>
{/literal}
{if !phpfox::isModule('socialbridge')}
<strong>
	{_p var='socialpublishers.please_install_social_bridge_plugin_first'}
</strong>
{else}
	<div class="error_message">
	<strong>
		{_p var='socialpublishers.all_your_socials_api_keys_will_be_stored_in'} <a href="{url link='admincp.socialbridge.providers'}" target="_blank">{url link='admincp.socialbridge.providers'}</a>
	</strong>
	</div>
	{foreach from=$aPublisherProviders index=iKey item=aPublisherProvider}
	<div class="table_header">
		{$aPublisherProvider.title} {_p var='socialpublishers.setting'}
	</div>
	<div class="table">
	    {if $aPublisherProvider.name == 'facebook' }
	    	{*<div class="tip" id="tip_facebook"><a href="javascript:void(0)" onclick="viewTutorial(this);" rel="tip_facebook_tutorial">{_p var='socialpublishers.view'}</a> {_p var='socialpublishers.tutorial_how_to_get_facebook_api_key'}</div>
	    	<div id="tip_facebook_tutorial" class="tip_tutorial" style="display: none">
	    		<ul>
	    			{_p var='socialpublishers.to_get_your_facebook_api_id'}
	    			<li>{_p var='socialpublishers.for_site_url_you_should_use_this_url'} <strong>{$sCoreUrl}</strong> </li>
	    			<li>Do follow Facebook steps to complete create new application.</li>
	
	    		</ul>
	    	</div>
	    	*}
	        <form action="{url link='admincp.socialpublishers.providers'}" method="post">
	                   {*  <div class="table">
	                        <div class="table_left" style="width: 200px;">{_p var='socialpublishers.application_id'}</div>
	                        <div class="table_right" style="margin-left:200px"><input type="text" size="40" value="{if isset($aPublisherProvider.params.app_id)}{$aPublisherProvider.params.app_id}{/if}" name="facebook[app_id]"></div>
	                    </div>
	                    <div class="table">
	                        <div class="table_left" style="width: 200px;">{_p var='socialpublishers.facebook_secret'}</div>
	                        <div class="table_right" style="margin-left:200px"><input type="text" size="60" value="{if isset($aPublisherProvider.params.secret)}{$aPublisherProvider.params.secret}{/if}" name="facebook[secret]"></div>
	                    </div>
	                   *}
	                    <div class="table">
	                        <div class="table_left" style="width: 200px;">
	                            {required}{_p var='socialpublishers.enable_facebook_connect'}
	                        </div>
	                        <div class="table_right" style="margin-left:200px">
	                            <div class="item_can_be_closed_holder">
	                                <span class="item_is_active">
	                                    <input type="radio" name="facebook[is_active]" value="1"  {if isset($aPublisherProvider.is_active) && $aPublisherProvider.is_active == 1}checked{/if}/> {_p var='admincp.yes'}
	                                </span>
	                                <span class=" item_is_not_active">
	                                    <input type="radio" name="facebook[is_active]" value="0" {if !isset($aPublisherProvider.is_active) || $aPublisherProvider.is_active == 0}checked{/if}/> {_p var='admincp.no'}
	                                 </span>
	                            </div>
	                        </div>
	                        <div class="clear"></div>
	                    </div>
	                    <div class="table_clear">
	                        <input type="submit" value="{_p var='core.submit'}" class="button" />
	                    </div>
	        </form>
	    {/if}
	    {if $aPublisherProvider.name == 'twitter' }
	    	{*<div class="tip" id="tip_twitter"><a href="javascript:void(0)" onclick="viewTutorial(this);" rel="tip_twitter_tutorial">{_p var='socialpublishers.view'}</a> {_p var='socialpublishers.tutorial_how_to_get_twitter_api_key'}</div>
	    	<div id="tip_twitter_tutorial" class="tip_tutorial" style="display: none">
	    		<ul>
	    			{_p var='socialpublishers.to_get_your_twitter_api'}
	    			<li>
	    				<div class="p_4">{_p var='socialpublishers.for_website_you_should_use_this_url'} <strong>{$sCoreUrl}</strong></div>
	    				<div class="p_4">{_p var='socialpublishers.for_callback_url_must_use'} <strong style="color:red;">{$sCallBackUrl}</strong></div>
	    				
	    			</li>
	    			{_p var='socialpublishers.do_follow_twitter_steps_to_complete_create_new_application'}
	    		</ul>
	    	</div>
	    	*}
	        <form action="{url link='admincp.socialpublishers.providers'}" method="post">
	                    {*
	                    <div class="table">
	                        <div class="table_left" style="width: 200px;">{_p var='socialpublishers.consumer_key'}</div>
	                        <div class="table_right" style="margin-left:200px"><input type="text" size="40" value="{if isset($aPublisherProvider.params.consumer_key)}{$aPublisherProvider.params.consumer_key}{/if}" name="twitter[consumer_key]"></div>
	                    </div>
	                    <div class="table">
	                        <div class="table_left" style="width: 200px;">{_p var='socialpublishers.consumer_secret'}</div>
	                        <div class="table_right" style="margin-left:200px"><input type="text" size="60" value="{if isset($aPublisherProvider.params.consumer_secret)}{$aPublisherProvider.params.consumer_secret}{/if}" name="twitter[consumer_secret]"></div>
	                    </div>
	                    *}
	                    <div class="table">
	                        <div class="table_left" style="width: 200px;">
	                            {required}{_p var='socialpublishers.enable_twitter_connect'}
	                        </div>
	                        <div class="table_right" style="margin-left:200px">
	                            <div class="item_can_be_closed_holder">
	                                <span class="item_is_active">
	                                    <input type="radio" name="twitter[is_active]" value="1" {if isset($aPublisherProvider.is_active) && $aPublisherProvider.is_active == 1}checked{/if} /> {_p var='admincp.yes'}
	                                </span>
	                                <span class=" item_is_not_active">
	                                    <input type="radio" name="twitter[is_active]" value="0" {if !isset($aPublisherProvider.is_active) || $aPublisherProvider.is_active == 0}checked{/if}/> {_p var='admincp.no'}
	                                 </span>
	                            </div>
	                        </div>
	                        <div class="clear"></div>
	                    </div>
	                    <div class="table_clear">
	                        <input type="submit" value="{_p var='core.submit'}" class="button" />
	                    </div>
	        </form>
	    {/if}
	     {if $aPublisherProvider.name == 'linkedin' }
	     	{*<div class="tip" id="tip_linkedin"><a href="javascript:void(0)" onclick="viewTutorial(this);" rel="tip_linkedin_tutorial">{_p var='socialpublishers.view'}</a> {_p var='socialpublishers.tutorial_how_to_get_linkedin_api_key'}</div>
	    	<div id="tip_linkedin_tutorial" class="tip_tutorial" style="display: none">
	    		<ul>
	    			{_p var='socialpublishers.to_get_your_linkedin_api_key'}
	    			{_p var='socialpublishers.do_follow_linkedin_steps_to_complete_create_new_application'}
	    		</ul>
	    	</div>
	    	*}
	        <form action="{url link='admincp.socialpublishers.providers'}" method="post">
	                   {*
	                     <div class="table">
	                        <div class="table_left" style="width: 200px;">{_p var='socialpublishers.api_key'}</div>
	                        <div class="table_right" style="margin-left:200px"><input type="text" size="40" value="{if isset($aPublisherProvider.params.api_key)}{$aPublisherProvider.params.api_key}{/if}" name="linkedin[api_key]"></div>
	                    </div>
	                    <div class="table">
	                        <div class="table_left" style="width: 200px;">{_p var='socialpublishers.secret_key'}</div>
	                        <div class="table_right" style="margin-left:200px"><input type="text" size="60" value="{if isset($aPublisherProvider.params.secret_key)}{$aPublisherProvider.params.secret_key}{/if}" name="linkedin[secret_key]"></div>
	                    </div>
	                   *}
	                    <div class="table">
	                        <div class="table_left" style="width: 200px;">
	                            {required}{_p var='socialpublishers.enable_linkedin_connect'}
	                        </div>
	                        <div class="table_right" style="margin-left:200px">
	                            <div class="item_can_be_closed_holder">
	                                <span class="item_is_active">
	                                    <input type="radio" name="linkedin[is_active]" value="1" {if isset($aPublisherProvider.is_active) && $aPublisherProvider.is_active == 1}checked{/if} > {_p var='admincp.yes'}
	                                </span>
	                                <span class=" item_is_not_active">
	                                    <input type="radio" name="linkedin[is_active]" value="0" {if !isset($aPublisherProvider.is_active) || $aPublisherProvider.is_active == 0}checked{/if}/> {_p var='admincp.no'}
	                                 </span>
	                            </div>
	                        </div>
	                        <div class="clear"></div>
	                    </div>
	                    <div class="table_clear">
	                        <input type="submit" value="{_p var='core.submit'}" class="button" />
	                    </div>
	        </form>
	    {/if}
	</div>
	{/foreach}
{/if}